<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Employee;
use Yii;
use common\models\Call;
use yii\data\SqlDataProvider;
use yii\db\Expression;
use yii\db\Query;
use common\models\Lead;
use common\models\Quote;
use common\models\UserGroupAssign;
use yii\helpers\VarDumper;
use common\models\Sms;
use common\models\Email;
use common\models\LeadFlow;

/**
 * AgentActivitySearch
 */
class AgentActivitySearch extends Call
{
    public $supervision_id;
    public $user_groups = [];

    public $date_range;
    public $date_from;
    public $date_to;
    public $id;
    public $username;

    public $s_project_id;
    public $s_type_id;

    public $e_project_id;
    public $e_type_id;

    public $project_id;
    public $from_status;
    public $to_status;
    public $lf_owner_id;
    public $isExtraQLostLeads = false;

     /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date_range','date_from', 'date_to', 'user_groups', 'id', 'username', 'c_project_id','c_call_type_id','s_type_id','s_project_id','project_id'], 'safe'],
            [['to_status', 'from_status'], 'safe'],

            [['lf_owner_id'], 'integer'],
            [['isExtraQLostLeads'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'date_range' => 'Date From-To',
            'date_from' => 'Date From',
            'date_to' => 'Date To',
            'user_groups' => 'Teams',
            'lf_owner_id' => 'OwnerId',
        ];
    }

   /*  public function afterValidate()
    {
        $this->date_from = !empty($this->date_from) ? date('Y-m-d 00:00:00', strtotime($this->date_from)) : '';
        $this->date_to = !empty($this->date_to) ? date('Y-m-d 23:59:59', strtotime($this->date_to)) : '';

        if (!empty($this->date_from) && empty($this->date_to)) {
            $this->date_to = date('Y-m-d 23:59:59', strtotime($this->date_from));
        } elseif (!empty($this->date_to) && empty($this->date_from)) {
            $this->date_from = date('Y-m-d 00:00:00', strtotime($this->date_to));
        }

        if (empty($this->date_from) && empty($this->date_to)) {
            $this->addError('date_from', 'Cannot be blank');
            $this->addError('date_to', 'Cannot be blank');
        }

        parent::afterValidate();
    } */

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * @param $params
     * @param $user Employee
     * @return SqlDataProvider
     */
    public function searchAgentLeads($params, $user): SqlDataProvider
    {
        $this->load($params);

        if (!$this->validate()) {
            $this->date_from = date('Y-m-d H:i');
            $this->date_to = date('Y-m-d H:i');
        }

        $query = new Query();

        $query->select(['e.id', 'e.username']);

        $date_from = Employee::convertTimeFromUserDtToUTC(strtotime($this->date_from));
        $date_to = Employee::convertTimeFromUserDtToUTC(strtotime($this->date_to));
        $between_condition = " BETWEEN '{$date_from}' AND '{$date_to}'";

        $query->addSelect(['(SELECT COUNT(*) FROM `call` WHERE (c_created_dt ' . $between_condition . ') AND c_created_user_id=e.id AND c_call_type_id = ' . Call::CALL_TYPE_IN . ') AS inbound_calls ']);
        $query->addSelect(['(SELECT COUNT(*) FROM `call` WHERE (c_created_dt ' . $between_condition . ') AND c_created_user_id=e.id AND c_call_type_id = ' . Call::CALL_TYPE_OUT . ') AS outbound_calls ']);
        $query->addSelect(['(SELECT SUM(c_call_duration) FROM `call` WHERE (c_created_dt ' . $between_condition . ') AND c_created_user_id=e.id) AS call_duration ']);
        $query->addSelect(['(SELECT SUM(c_recording_duration) FROM `call` WHERE (c_created_dt ' . $between_condition . ') AND c_created_user_id=e.id) AS call_recording_duration ']);

        $query->addSelect(['(SELECT COUNT(*) FROM sms WHERE (s_created_dt ' . $between_condition . ') AND s_created_user_id=e.id AND s_type_id = 1) AS sms_sent ']);
        $query->addSelect(['(SELECT COUNT(*) FROM sms WHERE (s_created_dt ' . $between_condition . ') AND s_created_user_id=e.id AND s_type_id = 2) AS sms_received ']);

        $query->addSelect(['(SELECT COUNT(*) FROM email WHERE (e_created_dt ' . $between_condition . ') AND e_created_user_id=e.id AND e_type_id = 1) AS email_sent ']);
        $query->addSelect(['(SELECT COUNT(*) FROM email WHERE (e_created_dt ' . $between_condition . ') AND e_created_user_id=e.id AND e_type_id = 2) AS email_received ']);

        $query->addSelect(['(SELECT COUNT(*) FROM quote_status_log WHERE (created ' . $between_condition . ') AND employee_id=e.id AND status = ' . Quote::STATUS_SENT . ') AS quotes_sent ']);

        $query->addSelect(['(SELECT COUNT(*) FROM lead_flow lf LEFT JOIN leads l ON lf.lead_id = l.id WHERE (lf.created ' . $between_condition . ') AND l.employee_id=e.id AND lf.status=' . Lead::STATUS_SOLD . ') AS st_sold ']);
        $query->addSelect(['(SELECT COUNT(*) FROM lead_flow lf WHERE (lf.created ' . $between_condition . ') AND lf.employee_id=e.id AND lf.status=' . Lead::STATUS_PROCESSING . ') AS st_processing ']);
        $query->addSelect(['(SELECT COUNT(*) FROM lead_flow lf WHERE (lf.created ' . $between_condition . ') AND lf.employee_id=e.id AND lf.status=' . Lead::STATUS_SNOOZE . ') AS st_snooze ']);
        $query->addSelect(['(SELECT COUNT(*) FROM lead_flow lf WHERE (lf.created ' . $between_condition . ') AND lf.employee_id=e.id AND lf.status=' . Lead::STATUS_PENDING . ') AS st_pending ']);

        $query->addSelect(['(SELECT COUNT(lf.id) FROM lead_flow lf WHERE (lf.created ' . $between_condition . ') AND lf.employee_id=e.id AND lf.status=' . Lead::STATUS_PROCESSING . ' AND lf.lf_from_status_id IS NULL) AS created_leads ']);
        $query->addSelect(['(SELECT COUNT(lf.id) FROM lead_flow lf LEFT JOIN leads l ON lf.lead_id = l.id WHERE l.clone_id IS NOT NULL AND (lf.created ' . $between_condition . ') AND lf.employee_id=e.id AND lf.status=' . Lead::STATUS_PROCESSING . ') AS cloned_leads ']);
        $query->addSelect(['(SELECT COUNT(*) FROM lead_flow WHERE (created ' . $between_condition . ') AND employee_id=e.id AND lf_from_status_id = ' . Lead::STATUS_PENDING . ' AND status=' . Lead::STATUS_PROCESSING . ') AS inbox_processing ']);
        $query->addSelect(['(SELECT COUNT(*) FROM lead_flow WHERE (created ' . $between_condition . ') AND employee_id=e.id AND lf_from_status_id = ' . Lead::STATUS_FOLLOW_UP . ' AND status=' . Lead::STATUS_PROCESSING . ') AS followup_processing ']);
        $query->addSelect(['(SELECT COUNT(*) FROM lead_flow WHERE (created ' . $between_condition . ') AND employee_id=e.id AND lf_from_status_id = ' . Lead::STATUS_PROCESSING . ' AND status=' . Lead::STATUS_TRASH . ') AS processing_trash ']);
        $query->addSelect(['(SELECT COUNT(*) FROM lead_flow WHERE (created ' . $between_condition . ') AND employee_id=e.id AND lf_from_status_id = ' . Lead::STATUS_PROCESSING . ' AND status=' . Lead::STATUS_FOLLOW_UP . ') AS processing_followup ']);
        $query->addSelect(['(SELECT COUNT(*) FROM lead_flow WHERE (created ' . $between_condition . ') AND employee_id=e.id AND lf_from_status_id = ' . Lead::STATUS_PROCESSING . ' AND status=' . Lead::STATUS_SNOOZE . ') AS processing_snooze ']);

        $query->addSelect(['(SELECT COUNT(*) FROM lead_task WHERE (lt_date ' . $between_condition . ') AND lt_user_id=e.id AND lt_completed_dt IS NULL) AS tasks_pending ']);
        $query->addSelect(['(SELECT COUNT(*) FROM lead_task WHERE (lt_date ' . $between_condition . ') AND lt_user_id=e.id) AS total_tasks ']);
        $query->addSelect(['(SELECT COUNT(*) FROM lead_task WHERE (lt_date ' . $between_condition . ') AND lt_user_id=e.id AND lt_completed_dt IS NOT NULL) AS completed_tasks ']);

        $query->addSelect(['extra_q_to_processing_query.extra_q_to_processing_cnt']);
        $query->addSelect(['processing_to_extra_q_query.processing_to_extra_q_cnt']);

        $query->from('employees AS e');

        $query->leftJoin([
            'extra_q_to_processing_query' => LeadFlow::find()
                ->select(['COUNT(*) AS extra_q_to_processing_cnt', 'lf_owner_id'])
                ->where(['BETWEEN', 'created', $date_from, $date_to])
                ->andWhere(['lf_from_status_id' => Lead::STATUS_EXTRA_QUEUE])
                ->andWhere(['status' => Lead::STATUS_PROCESSING])
                ->groupBy(['lf_owner_id'])
        ], 'e.id = extra_q_to_processing_query.lf_owner_id');

        $query->leftJoin([
            'processing_to_extra_q_query' => LeadFlow::find()
                ->alias('lead_flow_pe')
                ->select('COUNT(*) AS processing_to_extra_q_cnt')
                ->addSelect('lead_flow_pe.employee_id')
                ->andWhere(['lead_flow_pe.lf_from_status_id' => Lead::STATUS_PROCESSING])
                ->andWhere(['lead_flow_pe.status' => Lead::STATUS_EXTRA_QUEUE])
                ->innerJoin([
                    'first_extra_q_query' => LeadFlow::find()
                        ->select(['MIN(created) AS min_created_dt', 'lead_id'])
                        ->andWhere(['lf_from_status_id' => Lead::STATUS_PROCESSING])
                        ->andWhere(['status' => Lead::STATUS_EXTRA_QUEUE])
                        ->groupBy(['lead_id'])
                ], 'lead_flow_pe.lead_id = first_extra_q_query.lead_id AND lead_flow_pe.created = first_extra_q_query.min_created_dt')
                ->groupBy(['lead_flow_pe.employee_id'])
        ], 'e.id = processing_to_extra_q_query.employee_id');

        if ($user->isSupervision()) {
            $subQuery1 = UserGroupAssign::find()->select(['ugs_group_id'])->where(['ugs_user_id' => $user->id]);
            $subQuery = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])->where(['IN', 'ugs_group_id', $subQuery1])
                ->leftJoin('auth_assignment', 'auth_assignment.user_id = ugs_user_id')
                ->andWhere(['auth_assignment.item_name' => Employee::ROLE_AGENT])
                ->orWhere(['auth_assignment.user_id' => $user->id]);
            $query->andWhere(['IN', 'e.id', $subQuery]);
        }

        if (!empty($this->user_groups)) {
            $subQuery = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])->where(['IN', 'ugs_group_id', $this->user_groups]);
            $query->andWhere(['IN', 'e.id', $subQuery]);
        }

        // grid filtering conditions
        $query->andFilterWhere(['like', 'username', $this->username]);

        $command = $query->createCommand();
        $sql = $command->rawSql;

        $qCountEmployees = Employee::find();
        if ($this->username) {
            $qCountEmployees->andFilterWhere(['like', 'username', $this->username]);

            if ($user->isSupervision()) {
                $subQuery1 = UserGroupAssign::find()->select(['ugs_group_id'])->where(['ugs_user_id' => $user->id]);
                $subQuery = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])->where(['IN', 'ugs_group_id', $subQuery1])
                    ->leftJoin('auth_assignment', 'auth_assignment.user_id = ugs_user_id')
                    ->andWhere(['auth_assignment.item_name' => Employee::ROLE_AGENT])
                    ->orWhere(['auth_assignment.user_id' => $user->id]);
                $qCountEmployees->andWhere(['IN', 'id', $subQuery]);
            }

            if (!empty($this->user_groups)) {
                $subQuery = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])->where(['IN', 'ugs_group_id', $this->user_groups]);
                $qCountEmployees->andWhere(['IN', 'id', $subQuery]);
            }

            $totalEmployees = $qCountEmployees->count();
        } else {
            if ($user->isSupervision()) {
                $subQuery1 = UserGroupAssign::find()->select(['ugs_group_id'])->where(['ugs_user_id' => $user->id]);
                $subQuery = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])->where(['IN', 'ugs_group_id', $subQuery1])
                    ->leftJoin('auth_assignment', 'auth_assignment.user_id = ugs_user_id')
                    ->andWhere(['auth_assignment.item_name' => Employee::ROLE_AGENT])
                    ->orWhere(['auth_assignment.user_id' => $user->id]);
                $qCountEmployees->andWhere(['IN', 'id', $subQuery]);
            }

            if (!empty($this->user_groups)) {
                $subQuery = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])->where(['IN', 'ugs_group_id', $this->user_groups]);
                $qCountEmployees->andWhere(['IN', 'id', $subQuery]);
            }

            $totalEmployees = $qCountEmployees->count();
        }

        $paramsData = [
            'sql' => $sql,
            //'params' => [':publish' => 1],
            'totalCount' => $totalEmployees,
            'sort' => [
                'defaultOrder' => ['username' => SORT_ASC],
                'attributes' => [
                    'id' => [
                        'asc' => ['id' => SORT_ASC],
                        'desc' => ['id' => SORT_DESC],
                    ],
                    'username' => [
                        'asc' => ['username' => SORT_ASC],
                        'desc' => ['username' => SORT_DESC],
                        ],
                    'inbound_calls' => [
                        'asc' => ['inbound_calls' => SORT_ASC],
                        'desc' => ['inbound_calls' => SORT_DESC],
                    ],
                    'outbound_calls' => [
                        'asc' => ['outbound_calls' => SORT_ASC],
                        'desc' => ['outbound_calls' => SORT_DESC],
                    ],
                    'call_duration' => [
                        'asc' => ['call_duration' => SORT_ASC],
                        'desc' => ['call_duration' => SORT_DESC],
                    ],
                    'sms_sent' => [
                        'asc' => ['sms_sent' => SORT_ASC],
                        'desc' => ['sms_sent' => SORT_DESC],
                    ],
                    'sms_received' => [
                        'asc' => ['sms_received' => SORT_ASC],
                        'desc' => ['sms_received' => SORT_DESC],
                    ],
                    'email_sent' => [
                        'asc' => ['email_sent' => SORT_ASC],
                        'desc' => ['email_sent' => SORT_DESC],
                    ],
                    'email_received' => [
                        'asc' => ['email_received' => SORT_ASC],
                        'desc' => ['email_received' => SORT_DESC],
                    ],
                    'quotes_sent' => [
                        'asc' => ['quotes_sent' => SORT_ASC],
                        'desc' => ['quotes_sent' => SORT_DESC],
                    ],
                    'st_processing' => [
                        'asc' => ['st_processing' => SORT_ASC],
                        'desc' => ['st_processing' => SORT_DESC],
                    ],
                    'st_snooze' => [
                        'asc' => ['st_snooze' => SORT_ASC],
                        'desc' => ['st_snooze' => SORT_DESC],
                    ],
                    'inbox_processing' => [
                        'asc' => ['inbox_processing' => SORT_ASC],
                        'desc' => ['inbox_processing' => SORT_DESC],
                    ],
                    'followup_processing' => [
                        'asc' => ['followup_processing' => SORT_ASC],
                        'desc' => ['followup_processing' => SORT_DESC],
                    ],
                    'processing_trash' => [
                        'asc' => ['processing_trash' => SORT_ASC],
                        'desc' => ['processing_trash' => SORT_DESC],
                    ],
                    'processing_followup' => [
                        'asc' => ['processing_followup' => SORT_ASC],
                        'desc' => ['processing_followup' => SORT_DESC],
                    ],
                    'processing_snooze' => [
                        'asc' => ['processing_snooze' => SORT_ASC],
                        'desc' => ['processing_snooze' => SORT_DESC],
                    ],
                    'cloned_leads' => [
                        'asc' => ['cloned_leads' => SORT_ASC],
                        'desc' => ['cloned_leads' => SORT_DESC],
                    ],
                    'tasks_pending' => [
                        'asc' => ['tasks_pending' => SORT_ASC],
                        'desc' => ['tasks_pending' => SORT_DESC],
                    ],
                    'completed_tasks' => [
                        'asc' => ['completed_tasks' => SORT_ASC],
                        'desc' => ['completed_tasks' => SORT_DESC],
                    ],
                    'st_sold' => [
                        'asc' => ['st_sold' => SORT_ASC],
                        'desc' => ['st_sold' => SORT_DESC],
                    ],
                    'created_leads' => [
                        'asc' => ['created_leads' => SORT_ASC],
                        'desc' => ['created_leads' => SORT_DESC],
                    ],
                    'extra_q_to_processing_cnt' => [
                        'asc' => ['extra_q_to_processing_cnt' => SORT_ASC],
                        'desc' => ['extra_q_to_processing_cnt' => SORT_DESC],
                    ],
                    'processing_to_extra_q_cnt' => [
                        'asc' => ['processing_to_extra_q_cnt' => SORT_ASC],
                        'desc' => ['processing_to_extra_q_cnt' => SORT_DESC],
                    ],
                ],
            ],
            'pagination' => [
                'pageSize' => 35,
            ],
        ];

        return $dataProvider = new SqlDataProvider($paramsData);
    }

    public function searchCalls($params)
    {
        $query = Call::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->where(['c_created_user_id' => $this->id]);
        $query->andWhere(['between','c_created_dt', $this->date_from, $this->date_to]);

        $query->andFilterWhere([
            'c_project_id' => $this->c_project_id,
            'c_call_type_id' => $this->c_call_type_id,
        ]);

        //echo $query->createCommand()->rawSql;

        return $dataProvider;
    }

    public function searchSms($params)
    {
        $query = Sms::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->where(['s_created_user_id' => $this->id]);
        $query->andWhere(['between','s_created_dt', $this->date_from, $this->date_to]);

        $query->andFilterWhere([
            's_project_id' => $this->s_project_id,
            's_type_id' => $this->s_type_id,
        ]);

        return $dataProvider;
    }

    public function searchEmail($params)
    {
        $query = Email::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->where(['e_created_user_id' => $this->id]);
        $query->andWhere(['between','e_created_dt', $this->date_from, $this->date_to]);

        $query->andFilterWhere([
            'e_project_id' => $this->e_project_id,
            'e_type_id' => $this->e_type_id,
        ]);

        return $dataProvider;
    }

    public function searchClonedLeads($params)
    {
        $query = LeadFlow::find()->leftJoin('leads', 'lead_flow.lead_id = leads.id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->where(['lead_flow.employee_id' => $this->id , 'lead_flow.status' => Lead::STATUS_PROCESSING]);
        $query->andWhere(['NOT',['leads.clone_id' => null]]);
        $query->andWhere(['between','lead_flow.created', $this->date_from, $this->date_to]);
        $query->groupBy('lead_flow.lead_id');

        $query->andFilterWhere([
            'leads.project_id' => $this->project_id,
        ]);

       // echo $query->createCommand()->rawSql;

        return $dataProvider;
    }

    public function searchCreatedLeads($params)
    {
        $query = LeadFlow::find()->leftJoin('leads', 'lead_flow.lead_id = leads.id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->where(['lead_flow.employee_id' => $this->id , 'lead_flow.status' => Lead::STATUS_PROCESSING,'lead_flow.lf_from_status_id' => null]);
        $query->andWhere(['between','lead_flow.created', $this->date_from, $this->date_to]);
        //$query->groupBy('lead_flow.lead_id');

        $query->andFilterWhere([
            'leads.project_id' => $this->project_id,
        ]);

       /*  echo $query->createCommand()->rawSql;
        die; */

        return $dataProvider;
    }

    public function searchSoldLeads($params)
    {
        $query = LeadFlow::find()->leftJoin('leads', 'lead_flow.lead_id = leads.id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->where(['leads.employee_id' => $this->id , 'lead_flow.status' => Lead::STATUS_SOLD]);
        $query->andWhere(['between','lead_flow.created', $this->date_from, $this->date_to]);

        $query->andFilterWhere([
            'leads.project_id' => $this->project_id,
        ]);

        return $dataProvider;
    }

    public function searchFromToLeads($params)
    {
        $query = LeadFlow::find()->leftJoin('leads', 'lead_flow.lead_id = leads.id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andWhere(['between','lead_flow.created', $this->date_from, $this->date_to]);

        $query->andFilterWhere([
            'leads.project_id' => $this->project_id,
            'lead_flow.employee_id' => $this->id,
            'lead_flow.lf_owner_id' => $this->lf_owner_id,
            'lead_flow.status' => $this->to_status,
            'lead_flow.lf_from_status_id' => $this->from_status,
        ]);

        if ($this->isExtraQLostLeads) {
            $query->innerJoin([
                'first_extra_q_query' => LeadFlow::find()
                    ->select(['MIN(created) AS min_created_dt', 'lead_id'])
                    ->andWhere(['lf_from_status_id' => Lead::STATUS_PROCESSING])
                    ->andWhere(['status' => Lead::STATUS_EXTRA_QUEUE])
                    ->groupBy(['lead_id'])
            ], 'lead_flow.lead_id = first_extra_q_query.lead_id AND lead_flow.created = first_extra_q_query.min_created_dt');
        }

        return $dataProvider;
    }
}
