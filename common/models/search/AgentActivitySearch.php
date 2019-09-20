<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Employee;
use Yii;
use common\models\Call;
use yii\data\SqlDataProvider;
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

    public $s_project_id;
    public $s_type_id;

    public $e_project_id;
    public $e_type_id;

    public $project_id;
    public $from_status;
    public $to_status;


     /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date_range','date_from', 'date_to', 'user_groups','id','c_project_id','c_call_type_id','s_type_id','s_project_id','project_id'], 'safe'],
            [['to_status', 'from_status'], 'safe'],
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
    public function searchAgentLeads($params, $user)
    {
        $this->load($params);

        if (!$this->validate()) {
            $this->date_from = date('Y-m-d H:i');
            $this->date_to = date('Y-m-d H:i');
        }

        $query = new Query();
        $query->select(['e.id', 'e.username']);

        $between_condition = " BETWEEN '{$this->date_from}' AND '{$this->date_to}'";

        $query->addSelect(['(SELECT COUNT(*) FROM `call` WHERE (c_created_dt '.$between_condition.') AND c_created_user_id=e.id AND c_call_type_id = '.Call::CALL_TYPE_IN.') AS inbound_calls ']);
        $query->addSelect(['(SELECT COUNT(*) FROM `call` WHERE (c_created_dt '.$between_condition.') AND c_created_user_id=e.id AND c_call_type_id = '.Call::CALL_TYPE_OUT.') AS outbound_calls ']);
        $query->addSelect(['(SELECT SUM(c_call_duration) FROM `call` WHERE (c_created_dt '.$between_condition.') AND c_created_user_id=e.id) AS call_duration ']);
        $query->addSelect(['(SELECT SUM(c_recording_duration) FROM `call` WHERE (c_created_dt '.$between_condition.') AND c_created_user_id=e.id) AS call_recording_duration ']);

        $query->addSelect(['(SELECT COUNT(*) FROM sms WHERE (s_created_dt '.$between_condition.') AND s_created_user_id=e.id AND s_type_id = 1) AS sms_sent ']);
        $query->addSelect(['(SELECT COUNT(*) FROM sms WHERE (s_created_dt '.$between_condition.') AND s_created_user_id=e.id AND s_type_id = 2) AS sms_received ']);

        $query->addSelect(['(SELECT COUNT(*) FROM email WHERE (e_created_dt '.$between_condition.') AND e_created_user_id=e.id AND e_type_id = 1) AS email_sent ']);
        $query->addSelect(['(SELECT COUNT(*) FROM email WHERE (e_created_dt '.$between_condition.') AND e_created_user_id=e.id AND e_type_id = 2) AS email_received ']);

        $query->addSelect(['(SELECT COUNT(*) FROM quote_status_log WHERE (created '.$between_condition.') AND employee_id=e.id AND status = '.Quote::STATUS_SEND.') AS quotes_sent ']);

        $query->addSelect(['(SELECT COUNT(*) FROM lead_flow lf LEFT JOIN leads l ON lf.lead_id = l.id WHERE (lf.created '.$between_condition.') AND l.employee_id=e.id AND lf.status=' . Lead::STATUS_SOLD . ') AS st_sold ']);
        $query->addSelect(['(SELECT COUNT(*) FROM lead_flow lf WHERE (lf.created '.$between_condition.') AND lf.employee_id=e.id AND lf.status=' . Lead::STATUS_PROCESSING . ') AS st_processing ']);
        $query->addSelect(['(SELECT COUNT(*) FROM lead_flow lf WHERE (lf.created '.$between_condition.') AND lf.employee_id=e.id AND lf.status=' . Lead::STATUS_SNOOZE . ') AS st_snooze ']);
        $query->addSelect(['(SELECT COUNT(*) FROM lead_flow lf WHERE (lf.created '.$between_condition.') AND lf.employee_id=e.id AND lf.status=' . Lead::STATUS_PENDING . ') AS st_pending ']);

        $query->addSelect(['(SELECT COUNT(lf.id) FROM lead_flow lf WHERE (lf.created '.$between_condition.') AND lf.employee_id=e.id AND lf.status=' . Lead::STATUS_PROCESSING . ' AND lf.lf_from_status_id IS NULL) AS created_leads ']);
        $query->addSelect(['(SELECT COUNT(lf.id) FROM lead_flow lf LEFT JOIN leads l ON lf.lead_id = l.id WHERE l.clone_id IS NOT NULL AND (lf.created '.$between_condition.') AND lf.employee_id=e.id AND lf.status=' . Lead::STATUS_PROCESSING . ') AS cloned_leads ']);
        $query->addSelect(['(SELECT COUNT(*) FROM lead_flow WHERE (created '.$between_condition.') AND employee_id=e.id AND lf_from_status_id = '.Lead::STATUS_PENDING.' AND status=' . Lead::STATUS_PROCESSING . ') AS inbox_processing ']);
        $query->addSelect(['(SELECT COUNT(*) FROM lead_flow WHERE (created '.$between_condition.') AND employee_id=e.id AND lf_from_status_id = '.Lead::STATUS_FOLLOW_UP.' AND status=' . Lead::STATUS_PROCESSING . ') AS followup_processing ']);
        $query->addSelect(['(SELECT COUNT(*) FROM lead_flow WHERE (created '.$between_condition.') AND employee_id=e.id AND lf_from_status_id = '.Lead::STATUS_PROCESSING.' AND status=' . Lead::STATUS_TRASH . ') AS processing_trash ']);
        $query->addSelect(['(SELECT COUNT(*) FROM lead_flow WHERE (created '.$between_condition.') AND employee_id=e.id AND lf_from_status_id = '.Lead::STATUS_PROCESSING.' AND status=' . Lead::STATUS_FOLLOW_UP . ') AS processing_followup ']);
        $query->addSelect(['(SELECT COUNT(*) FROM lead_flow WHERE (created '.$between_condition.') AND employee_id=e.id AND lf_from_status_id = '.Lead::STATUS_PROCESSING.' AND status=' . Lead::STATUS_SNOOZE . ') AS processing_snooze ']);

        $query->addSelect(['(SELECT COUNT(*) FROM lead_task WHERE (lt_date '.$between_condition.') AND lt_user_id=e.id AND lt_completed_dt IS NULL) AS tasks_pending ']);
        $query->addSelect(['(SELECT COUNT(*) FROM lead_task WHERE (lt_date '.$between_condition.') AND lt_user_id=e.id) AS total_tasks ']);
        $query->addSelect(['(SELECT COUNT(*) FROM lead_task WHERE (lt_date '.$between_condition.') AND lt_user_id=e.id AND lt_completed_dt IS NOT NULL) AS completed_tasks ']);

        $query->from('employees AS e');

        /*if ($this->supervision_id > 0) {
            $subQuery1 = UserGroupAssign::find()->select(['ugs_group_id'])->where(['ugs_user_id' => $this->supervision_id]);
            $subQuery = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])->where(['IN', 'ugs_group_id', $subQuery1]);
            $query->andWhere(['IN', 'e.id', $subQuery]);
        }*/

        if ($user->isSupervision()) {
            $subQuery1 = UserGroupAssign::find()->select(['ugs_group_id'])->where(['ugs_user_id' => $user->id]);
            $subQuery = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])->where(['IN', 'ugs_group_id', $subQuery1])
                ->leftJoin('auth_assignment', 'auth_assignment.user_id = ugs_user_id')
                ->andWhere(['auth_assignment.item_name' => Employee::ROLE_AGENT])
                ->orWhere(['auth_assignment.user_id' => $user->id]);
            $query->andWhere(['IN', 'e.id', $subQuery]);
        }

        if (!empty($this->user_groups)){
            $subQuery = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])->where(['IN', 'ugs_group_id', $this->user_groups]);
            $query->andWhere(['IN', 'e.id', $subQuery]);
        }

        //$totalCount = 20;

        $command = $query->createCommand();
        $sql = $command->rawSql;

        //var_dump($sql);die;

        $paramsData = [
            'sql' => $sql,
            //'params' => [':publish' => 1],
            //'totalCount' => $totalCount,
            'sort' => [
                'defaultOrder' => ['username' => SORT_ASC],
                'attributes' => [
                    'username' => [
                        'asc' => ['username' => SORT_ASC],
                        'desc' => ['username' => SORT_DESC],
                        'label' => 'Agent',
                    ],

                ],
            ],
            'pagination' => [
                'pageSize' => 25,
            ],
        ];

        $dataProvider = new SqlDataProvider($paramsData);
        return $dataProvider;
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
        $query = LeadFlow::find()->leftJoin('leads','lead_flow.lead_id = leads.id');

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
        $query = LeadFlow::find()->leftJoin('leads','lead_flow.lead_id = leads.id');

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
        $query = LeadFlow::find()->leftJoin('leads','lead_flow.lead_id = leads.id');

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
        $query = LeadFlow::find()->leftJoin('leads','lead_flow.lead_id = leads.id');

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

        $query->where(['lead_flow.employee_id' => $this->id ,'lead_flow.status' => $this->to_status, 'lead_flow.lf_from_status_id' => $this->from_status]);
        $query->andWhere(['between','lead_flow.created', $this->date_from, $this->date_to]);

        $query->andFilterWhere([
            'leads.project_id' => $this->project_id,
        ]);

        return $dataProvider;
    }

}
