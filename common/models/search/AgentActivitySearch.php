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

/**
 * AgentActivitySearch
 */
class AgentActivitySearch extends Model
{
    public $supervision_id;
    public $user_groups = [];

    public $date_from;
    public $date_to;

     /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date_from', 'date_to', 'user_groups'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'date_from' => 'Date From',
            'date_to' => 'Date To',
            'user_groups' => 'Teams',
        ];
    }

    public function afterValidate()
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
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }


    public function searchAgentLeads($params)
    {
        $this->load($params);

        if (!$this->validate()) {
            $this->date_from = date('Y-m-d 00:00');
            $this->date_to = date('Y-m-d 23:59');
        }

        $query = new Query();
        $query->select(['e.id', 'e.username']);

        $between_condition = " BETWEEN '{$this->date_from}' AND '{$this->date_to}'";

        $query->addSelect(['(SELECT COUNT(*) FROM `call` WHERE (c_created_dt '.$between_condition.') AND c_created_user_id=e.id AND c_direction LIKE "inbound") AS inbound_calls ']);
        $query->addSelect(['(SELECT COUNT(*) FROM `call` WHERE (c_created_dt '.$between_condition.') AND c_created_user_id=e.id AND c_direction LIKE "outbound%") AS outbound_calls ']);
        $query->addSelect(['(SELECT SUM(c_call_duration) FROM `call` WHERE (c_created_dt '.$between_condition.') AND c_created_user_id=e.id) AS call_duration ']);

        $query->addSelect(['(SELECT COUNT(*) FROM sms WHERE (s_created_dt '.$between_condition.') AND s_created_user_id=e.id AND s_type_id = 1) AS sms_sent ']);
        $query->addSelect(['(SELECT COUNT(*) FROM sms WHERE (s_created_dt '.$between_condition.') AND s_created_user_id=e.id AND s_type_id = 2) AS sms_received ']);

        $query->addSelect(['(SELECT COUNT(*) FROM email WHERE (e_created_dt '.$between_condition.') AND (e_email_from = e.email OR e_email_to = e.email) AND e_type_id = 1) AS email_sent ']);
        $query->addSelect(['(SELECT COUNT(*) FROM email WHERE (e_created_dt '.$between_condition.') AND (e_email_from = e.email OR e_email_to = e.email) AND e_type_id = 2) AS email_received ']);

        $query->addSelect(['(SELECT COUNT(*) FROM quote_status_log WHERE (created '.$between_condition.') AND employee_id=e.id AND status = '.Quote::STATUS_SEND.') AS quotes_sent ']);

        $query->addSelect(['(SELECT COUNT(*) FROM leads WHERE (created '.$between_condition.') AND employee_id=e.id AND status=' . Lead::STATUS_SOLD . ') AS st_sold ']);
        $query->addSelect(['(SELECT COUNT(*) FROM leads WHERE (created '.$between_condition.') AND employee_id=e.id AND status=' . Lead::STATUS_PROCESSING . ') AS st_processing ']);
        $query->addSelect(['(SELECT COUNT(*) FROM leads WHERE (created '.$between_condition.') AND employee_id=e.id AND status=' . Lead::STATUS_SNOOZE . ') AS st_snooze ']);
        $query->addSelect(['(SELECT COUNT(*) FROM leads WHERE (created '.$between_condition.') AND employee_id=e.id AND status=' . Lead::STATUS_PENDING . ') AS st_pending ']);

        $query->addSelect(['(SELECT COUNT(*) FROM lead_flow WHERE (created '.$between_condition.') AND employee_id=e.id AND status=' . Lead::STATUS_PROCESSING . ') AS created_leads ']);
        $query->addSelect(['(SELECT COUNT(lf.id) FROM lead_flow lf LEFT JOIN leads l ON lf.lead_id = l.clone_id WHERE l.id IS NOT NULL AND (lf.created '.$between_condition.') AND lf.employee_id=e.id AND lf.status=' . Lead::STATUS_PROCESSING . ') AS cloned_leads ']);
        $query->addSelect(['(SELECT COUNT(*) FROM lead_flow WHERE (created '.$between_condition.') AND employee_id=e.id AND lf_from_status_id = '.Lead::STATUS_PENDING.' AND status=' . Lead::STATUS_PROCESSING . ') AS inbox_processing ']);
        $query->addSelect(['(SELECT COUNT(*) FROM lead_flow WHERE (created '.$between_condition.') AND employee_id=e.id AND lf_from_status_id = '.Lead::STATUS_FOLLOW_UP.' AND status=' . Lead::STATUS_PROCESSING . ') AS followup_processing ']);
        $query->addSelect(['(SELECT COUNT(*) FROM lead_flow WHERE (created '.$between_condition.') AND employee_id=e.id AND lf_from_status_id = '.Lead::STATUS_PROCESSING.' AND status=' . Lead::STATUS_TRASH . ') AS processing_trash ']);
        $query->addSelect(['(SELECT COUNT(*) FROM lead_flow WHERE (created '.$between_condition.') AND employee_id=e.id AND lf_from_status_id = '.Lead::STATUS_PROCESSING.' AND status=' . Lead::STATUS_FOLLOW_UP . ') AS processing_followup ']);
        $query->addSelect(['(SELECT COUNT(*) FROM lead_flow WHERE (created '.$between_condition.') AND employee_id=e.id AND lf_from_status_id = '.Lead::STATUS_PROCESSING.' AND status=' . Lead::STATUS_SNOOZE . ') AS processing_snooze ']);

        $query->addSelect(['(SELECT COUNT(*) FROM lead_task WHERE (lt_date '.$between_condition.') AND lt_user_id=e.id AND lt_completed_dt IS NULL) AS tasks_pending ']);
        $query->addSelect(['(SELECT COUNT(*) FROM lead_task WHERE (lt_date '.$between_condition.') AND lt_user_id=e.id) AS total_tasks ']);
        $query->addSelect(['(SELECT COUNT(*) FROM lead_task WHERE (lt_date '.$between_condition.') AND lt_user_id=e.id AND lt_completed_dt IS NOT NULL) AS completed_tasks ']);

        $query->from('employees AS e');

        if ($this->supervision_id > 0) {
            $subQuery1 = UserGroupAssign::find()->select(['ugs_group_id'])->where(['ugs_user_id' => $this->supervision_id]);
            $subQuery = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])->where(['IN', 'ugs_group_id', $subQuery1]);
            $query->andWhere(['IN', 'e.id', $subQuery]);
        }
        if (!empty($this->user_groups)){
            $subQuery = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])->where(['IN', 'ugs_group_id', $this->user_groups]);
            $query->andWhere(['IN', 'e.id', $subQuery]);
        }

        /* $subQuery = new Query();
        $subQuery->from('lead_flow AS lf')->where('lf.created '.$between_condition); */

        //$query->leftJoin(['lf' => $subQuery],'lf.employee_id = e.id');

        $totalCount = 20;

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
                'pageSize' => 20,
            ],
        ];

        $dataProvider = new SqlDataProvider($paramsData);
        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $count = Yii::$app->db->createCommand('SELECT COUNT(*) FROM employees e', [])->queryScalar();

        $sql = 'SELECT e.id, e.username, SUM(CASE WHEN c.c_direction LIKE "inbound" THEN 1 ELSE 0 END) as inbound,
SUM(CASE WHEN c.c_direction LIKE "outbound%" THEN 1 ELSE 0 END) as outbound,
SUM(c.c_call_duration) call_duration,
SUM(CASE WHEN s.s_type_id = 1 THEN 1 ELSE 0 END) as sms_sent,
SUM(CASE WHEN s.s_type_id = 2 THEN 1 ELSE 0 END) as sms_received,
SUM(CASE WHEN em.e_type_id = 1 THEN 1 ELSE 0 END) as email_sent,
SUM(CASE WHEN em.e_type_id = 2 THEN 1 ELSE 0 END) as email_received
FROM employees e
LEFT JOIN `call` c ON c.c_created_user_id = e.id
LEFT JOIN sms s ON s.s_created_user_id = e.id AND s.s_type_id != 0
LEFT JOIN email em ON (em.e_email_from = e.email OR em.e_email_to = e.email) AND em.e_type_id != 0
GROUP BY e.id';

        $dataProvider = new SqlDataProvider([
            'sql' => $sql,
            'params' => [],
            'totalCount' => $count,
            'sort' => [
                'attributes' => [
                    'id',
                ],
            ],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $dataProvider;
    }

}
