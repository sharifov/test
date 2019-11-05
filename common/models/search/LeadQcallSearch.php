<?php

namespace common\models\search;

use common\models\Call;
use common\models\ClientPhone;
use common\models\Employee;
use common\models\Lead;
use common\models\LeadFlow;
use sales\access\EmployeeProjectAccess;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\LeadQcall;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\VarDumper;

/**
 * LeadQcallSearch represents the model behind the search form of `common\models\LeadQcall`.
 *
 * @property string $current_dt
 * @property $projectId
 * @property $leadStatus
 * @property $cabin
 * @property $attempts
 * @property $deadline
 */
class LeadQcallSearch extends LeadQcall
{
    public $current_dt;
    public $projectId;
    public $leadStatus;
    public $cabin;
    public $attempts;
    public $deadline;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lqc_lead_id', 'lqc_weight'], 'integer'],

            [['lqc_dt_from', 'lqc_dt_to', 'current_dt'], 'safe'],

            ['projectId', 'integer'],
            ['leadStatus', 'integer'],
            ['cabin', 'in', 'range' => array_keys(Lead::CABIN_LIST)],
            ['attempts', 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
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
        $query = LeadQcall::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['lqc_weight' => SORT_ASC, 'lqc_dt_from' => SORT_ASC]],
            'pagination' => [
                'pageSize' => 40,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'lqc_lead_id' => $this->lqc_lead_id,
            'lqc_dt_from' => $this->lqc_dt_from,
            'lqc_dt_to' => $this->lqc_dt_to,
            'lqc_weight' => $this->lqc_weight,
        ]);

        return $dataProvider;
    }


    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function searchList($params): ActiveDataProvider
    {
        $query = LeadQcall::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['lqc_weight' => SORT_ASC, 'lqc_dt_from' => SORT_ASC]],
            'pagination' => [
                'pageSize' => 40,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->current_dt) {
            $current_dt = Employee::convertTimeFromUserDtToUTC(strtotime($this->current_dt));
            //echo $current_dt; exit;
            $query->andWhere(['<=', 'lqc_dt_from', $current_dt]);
            //$query->andWhere(['>=', 'lqc_dt_to', $current_dt]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'lqc_lead_id' => $this->lqc_lead_id,
            'lqc_dt_from' => $this->lqc_dt_from,
            'lqc_dt_to' => $this->lqc_dt_to,
            'lqc_weight' => $this->lqc_weight,
        ]);

        $query->with(['lqcLead', 'lqcLead.project', 'lqcLead.source', 'lqcLead.employee']);

        return $dataProvider;
    }

    /**
     * @param $params
     * @param Employee $user
     * @return ActiveDataProvider
     */
    public function searchByRedial($params, Employee $user): ActiveDataProvider
    {
        $query = self::find()->select('*');

        $query->with(['lqcLead.project', 'lqcLead.leadFlightSegments', 'lqcLead.source', 'lqcLead.employee', 'lqcLead.client.clientPhones']);

        $query->joinWith('lqcLead');

        $query->andWhere([Lead::tableName() . '.project_id' => array_keys(EmployeeProjectAccess::getProjects($user))]);

        $query->andWhere(['<=', 'lqc_dt_from', date('Y-m-d H:i:s')]);

        if ($user->isAgent() || $user->isSupervision()) {
            $query->andWhere(['NOT IN', 'l_call_status_id', [Lead::CALL_STATUS_PROCESS, Lead::CALL_STATUS_PREPARE]]);
            $query->andWhere([Lead::tableName() . '.status' => Lead::STATUS_PENDING]);
        }

        $query->addSelect([
            'countClientPhones' => (new Query())
                ->select('count(*)')
                ->from(ClientPhone::tableName())
                ->andWhere(ClientPhone::tableName() . '.client_id = ' . Lead::tableName() . '.client_id')
        ]);
        $query->andHaving(['>', 'countClientPhones', 0]);

        $query->addSelect([
            'attempts' => (new Query())
                ->select('lf_out_calls')
                ->from(LeadFlow::tableName())
                ->andWhere(LeadFlow::tableName() . '.lead_id = lqc_lead_id')
                ->orderBy([LeadFlow::tableName() . '.id' => SORT_DESC])
                ->limit(1)
        ]);

        $deadlineExpr = "(FLOOR(TIMESTAMPDIFF(SECOND, '" . date("Y-m-d H:i:s") . "', lqc_dt_to )/60))";
        $query->addSelect(['deadline' =>
            new Expression("if (" . $deadlineExpr . " > 0, " . $deadlineExpr . " , 0) ")
        ]);

        $query->addOrderBy([
            'deadline' => SORT_ASC,
            'attempts' => SORT_ASC,
            'lqc_dt_from' => SORT_ASC
        ]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
//            'sort'=> [
//                'defaultOrder' => [
//                    'lqc_dt_to' => SORT_ASC,
//                    'attempts' => SORT_ASC,
//                    'lqc_dt_from' => SORT_ASC,
//                ]
//            ],
            /*'pagination' => [
                'pageSize' => 40,
            ],*/
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if (!$user->isAgent()) {
            $query->andFilterWhere(['lqc_lead_id' => $this->lqc_lead_id]);
        }

        $query->andFilterWhere([
            Lead::tableName() . '.project_id' => $this->projectId,
            Lead::tableName() . '.status' => $this->leadStatus,
            Lead::tableName() . '.cabin' => $this->cabin,
        ]);

//        VarDumper::dump($query->createCommand()->getRawSql());die;
        return $dataProvider;
    }

    /**
     * @param $params
     * @param Employee $user
     * @return ActiveDataProvider
     */
    public function searchLastCalls($params, Employee $user): ActiveDataProvider
    {
        $query = self::find()->select('*');

        $query->with(['lqcLead.project', 'lqcLead.leadFlightSegments', 'lqcLead.source', 'lqcLead.employee', 'lqcLead.client.clientPhones']);

        $query->joinWith('lqcLead');

        $query->andWhere([Lead::tableName() . '.project_id' => array_keys(EmployeeProjectAccess::getProjects($user))]);

        if ($user->isAgent() || $user->isSupervision()) {
            $query->andWhere(['NOT IN', 'l_call_status_id', [Lead::CALL_STATUS_PROCESS, Lead::CALL_STATUS_PREPARE]]);
            $query->andWhere([Lead::tableName() . '.status' => Lead::STATUS_PENDING]);
        }

        $query->addSelect([
            'countClientPhones' => (new Query())
                ->select('count(*)')
                ->from(ClientPhone::tableName())
                ->andWhere(ClientPhone::tableName() . '.client_id = ' . Lead::tableName() . '.client_id')
        ]);
        $query->andHaving(['>', 'countClientPhones', 0]);

        $query->addSelect([
            'attempts' => (new Query())
                ->select('lf_out_calls')
                ->from(LeadFlow::tableName())
                ->andWhere(LeadFlow::tableName() . '.lead_id = lqc_lead_id')
                ->orderBy([LeadFlow::tableName() . '.id' => SORT_DESC])
                ->limit(1)
        ]);

        $deadlineExpr = "(FLOOR(TIMESTAMPDIFF(SECOND, '" . date("Y-m-d H:i:s") . "', lqc_dt_to )/60))";
        $query->addSelect(['deadline' =>
            new Expression("if (" . $deadlineExpr . " > 0, " . $deadlineExpr . " , 0) ")
        ]);

        $query->andWhere([
            'lqc_lead_id' => Call::find()
                ->select('c_lead_id')
                ->andWhere([
                    'c_lead_id' => self::find()->select('lqc_lead_id'),
                    'c_call_type_id' => Call::CALL_TYPE_OUT,
                    'c_created_user_id' => $user->id,
                    'c_source_type_id' => Call::SOURCE_REDIAL_CALL
                ])
//                    ->andWhere(['IS NOT', 'c_parent_id', null])
                ->groupBy(['c_lead_id'])
                ->orderBy('MAX(c_id)')
        ]);

        $query->addOrderBy(['l_last_action_dt' => SORT_DESC]);

        $query->limit((int)\Yii::$app->params['settings']['qcall_count_last_dialed_leads']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort' => false
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        return $dataProvider;
    }
}
