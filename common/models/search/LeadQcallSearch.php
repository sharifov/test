<?php

namespace common\models\search;

use common\models\UserOnline;
use common\models\UserProfile;
use sales\helpers\query\QueryHelper;
use sales\services\lead\qcall\DayTimeHours;
use Yii;
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
 * @property $l_is_test
 * @property $l_call_status_id
 */
class LeadQcallSearch extends LeadQcall
{
    private const PROJECT_ARANGRANT = 7;

    public $current_dt;
    public $projectId;
    public $leadStatus;
    public $cabin;
    public $attempts;
    public $deadline;
    public $l_is_test;
    public $l_call_status_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lqc_lead_id', 'lqc_weight', 'l_is_test', 'deadline'], 'integer'],

            [['current_dt', 'l_is_test', 'deadline'], 'safe'],

            ['lqc_created_dt', 'date', 'format' => 'php:Y-m-d'],
            ['lqc_dt_from', 'date', 'format' => 'php:Y-m-d'],
            ['lqc_dt_to', 'date', 'format' => 'php:Y-m-d'],

            ['projectId', 'integer'],
            ['leadStatus', 'integer'],
            ['cabin', 'in', 'range' => array_keys(Lead::CABIN_LIST)],
            ['l_is_test', 'in', 'range' => [0,1]],
            ['attempts', 'integer'],
            ['l_call_status_id', 'integer'],
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
        $nowDt = date('Y-m-d H:i:s');
        $query = self::find()->select('*');

        $query->with(['lqcLead.project', 'lqcLead.leadFlightSegments', 'lqcLead.source', 'lqcLead.employee', 'lqcLead.client.clientPhones']);

        $query->joinWith('lqcLead');

        $query->andWhere([Lead::tableName() . '.project_id' => array_keys(EmployeeProjectAccess::getProjects($user))]);

        $query->addSelect([
            'is_not_empty_passengers' => new Expression('if ((' . Lead::tableName() . '.adults > 0 OR ' . Lead::tableName() . '.children > 0 OR ' . Lead::tableName() . '.infants > 0), 1, 2)')
        ]);

        $query->addSelect([
            'is_ready' => new Expression('if (lqc_dt_from <= \'' . $nowDt . '\', 1, 0)')
        ]);

        $query->addSelect([
            'is_reserved' => new Expression('if (lqc_reservation_time > \'' . $nowDt . '\' AND (lqc_reservation_user_id != ' . $user->id . ' OR lqc_reservation_user_id IS NULL) , 1, 0)')
        ]);

        $defaultOrder = [
            'is_not_empty_passengers' => SORT_ASC,
            'is_ready' => SORT_DESC,
            'is_reserved' => SORT_ASC,
        ];

        if (!$user->isAdmin()) {
            $query->andHaving(['<>', 'is_ready', 0]);
            $query->andHaving(['<>', 'is_reserved', 1]);
//            $query->andWhere(['<=', 'lqc_dt_from', $nowDt]);
//            $query->andWhere(['or',
//                ['<=', 'lqc_reservation_time', $nowDt],
//                ['IS', 'lqc_reservation_time', null]
//            ]);
        }

        if ($user->isAgent() || $user->isSupervision()) {
            $query->andWhere(['NOT IN', 'l_call_status_id', [
                Lead::CALL_STATUS_PROCESS,
                Lead::CALL_STATUS_PREPARE,
                Lead::CALL_STATUS_QUEUE,
                Lead::CALL_STATUS_BUGGED,
            ]]);
            $query->andWhere([Lead::tableName() . '.status' => Lead::STATUS_PENDING]);
        }

        if (empty($params['is_test']) && !$user->checkIfUsersIpIsAllowed()) {
			$query->andWhere([Lead::tableName() . '.l_is_test' => 0]);
		}

        $redialSame = (int)Yii::$app->params['settings']['redial_same_deadline_priority'];
        $samePriority = "TIMESTAMPDIFF(MINUTE, '" . $nowDt . "', lqc_dt_to)";
        $query->addSelect([
            'same_priority' =>
                new Expression('if (' . $samePriority . ' <= ' . $redialSame . ', 1, 0) ')
        ]);

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

        $deadlineExpr = "(FLOOR(TIMESTAMPDIFF(SECOND, '" . $nowDt . "', lqc_dt_to )/60))";
        $query->addSelect(['deadline' =>
            new Expression('if (' . $deadlineExpr . ' > 0, ' . $deadlineExpr . ' , 0) ')
        ]);

//        $query->addSelect(['expired' =>
//            new Expression("if (" . $deadlineExpr . " <= 0, " . $deadlineExpr . " , 1) ")
//        ]);

        if (($freshTime = (int)Yii::$app->params['settings']['redial_fresh_time']) > 0) {
            $expression = "TIMESTAMPDIFF(MINUTE, lqc_created_dt, '" . $nowDt . "')";
            $query->addSelect(['isFresh' =>
                new Expression('if (' . $expression . ' <= ' . $freshTime . ', 1, 0) ')
            ]);
//            $query->addOrderBy([
//               'isFresh' => SORT_DESC
//            ]);

            $dayTimeHours = new DayTimeHours(Yii::$app->params['settings']['qcall_day_time_hours']);
            $clientGmt = "TIME( CONVERT_TZ(NOW(), '+00:00', " . Lead::tableName() . '.offset_gmt) )';
//            $query->addSelect(['client_gmt' => new Expression($clientGmt)]);
            $query->addSelect(['is_in_day_time_hours' =>
                new Expression('if ( '.$expression . ' > ' . $freshTime .'  AND ' . $clientGmt . ' >= \'' . $dayTimeHours->getStart() . '\' AND ' . $clientGmt . ' <= \'' . $dayTimeHours->getEnd() . '\', 1, 0) ')
            ]);
//            $query->addOrderBy([
//                'is_in_day_time_hours' => SORT_DESC
//            ]);

            $defaultOrder = array_merge($defaultOrder, [
				'isFresh' => SORT_DESC,
				'is_in_day_time_hours' => SORT_DESC
			]);

        } else {
            $dayTimeHours = new DayTimeHours(Yii::$app->params['settings']['qcall_day_time_hours']);
            $clientGmt = "TIME( CONVERT_TZ(NOW(), '+00:00', " . Lead::tableName() . '.offset_gmt) )';
//            $query->addSelect(['client_gmt' => new Expression($clientGmt)]);
            $query->addSelect(['is_in_day_time_hours' =>
                new Expression('if (' . $clientGmt . ' >= \'' . $dayTimeHours->getStart() . '\' AND ' . $clientGmt . ' <= \'' . $dayTimeHours->getEnd() . '\', 1, 0) ')
            ]);
//            $query->addOrderBy([
//                'is_in_day_time_hours' => SORT_DESC
//            ]);

            $defaultOrder = array_merge($defaultOrder, [
                'is_in_day_time_hours' => SORT_DESC
            ]);
        }

        if (($settingsMinute = (int)Yii::$app->params['settings']['redial_business_flight_leads_skill_priority_time']) > 0) {

            $expression = "TIMESTAMPDIFF(MINUTE, lqc_created_dt, '" . $nowDt . "')";

            $query->addSelect(['redial_business_flight_leads_over_created_time' =>
                new Expression('if (' . $expression . ' > ' . $settingsMinute . ', 1, 0) ')
            ]);

            $skillSettings = (int)Yii::$app->params['settings']['redial_business_flight_leads_minimum_skill_level'];
            $userSkill = $user->userProfile ? (int) $user->userProfile->up_skill : 0;

            $query->addSelect(['redial_business_flight_leads_skill_current_user' =>
                new Expression('if (' . $userSkill . ' >= ' . $skillSettings . ', 1, 0) ')
            ]);

            $countUsers = (int)UserProfile::find()->select('count(*)')->andWhere([
                'up_user_id' =>
                    UserOnline::find()->select(['uo_user_id'])->indexBy('uo_user_id')
            ])->andWhere('up_skill >= ' . $skillSettings)->count();
            $query->addSelect(['redial_business_flight_leads_skill_online_user' =>
                new Expression('if (' . $countUsers . ' < 1, 1, 0) ')
            ]);

            $query->andHaving(
                new Expression(
                    '(redial_business_flight_leads_over_created_time OR redial_business_flight_leads_skill_current_user OR redial_business_flight_leads_skill_online_user)' .
                    'OR (' . Lead::tableName() . '.project_id <> ' . self::PROJECT_ARANGRANT . ' AND ' . Lead::tableName() . '.cabin NOT IN ("' . Lead::CABIN_BUSINESS . '", "' . Lead::CABIN_FIRST . '")) '
                )
            );
        }

//        $query->addOrderBy([
////            'expired' => SORT_DESC,
//            'deadline' => SORT_ASC,
//            'attempts' => SORT_ASC,
//            'lqc_dt_from' => SORT_ASC
//        ]);

        $defaultOrder = array_merge($defaultOrder, [
            'same_priority' => SORT_DESC,
            'lqc_weight' => SORT_ASC,
            'deadline' => SORT_ASC,
            'attempts' => SORT_ASC,
            'lqc_dt_from' => SORT_ASC,
            'lqc_lead_id' => SORT_ASC,
        ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder' => $defaultOrder,
				'attributes' => [
				    'is_ready',
                    'is_reserved',
                    'isFresh',
                    'is_in_day_time_hours',
                    'same_priority',
                    'lqc_weight',
                    'deadline',
                    'attempts',
                    'lqc_dt_from',
                    'lqc_dt_to',
                    'lqc_created_dt',
                    'lqc_lead_id',
                    'is_not_empty_passengers',
				]
            ],
            /*'pagination' => [
                'pageSize' => 40,
            ],*/
        ]);

        if ((bool)Yii::$app->params['settings']['enable_redial_show_lead_limit'] && $user->userParams->up_inbox_show_limit_leads !== null) {
            $query->limit((int)$user->userParams->up_inbox_show_limit_leads);
            $dataProvider->pagination = false;
        }

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
			Lead::tableName() . '.l_is_test' => $this->l_is_test,
        ]);


        $dataProvider->sort->attributes['l_is_test'] = [
        	'asc' => ['l_is_test' => SORT_ASC],
        	'desc' => ['l_is_test' => SORT_DESC],
		];

//        VarDumper::dump($dataProvider);die;
        if ($user->isAdmin()) {

            $query->andFilterWhere([
                Lead::tableName() . '.l_call_status_id' => $this->l_call_status_id
            ]);

            if ($this->attempts === '0') {
                $query->andHaving(['attempts' => 0]);

            } else {
                $query->andFilterHaving(['attempts' => $this->attempts]);
            }

            if ($this->lqc_dt_from) {
                QueryHelper::dayEqualByUserTZ($query, 'lqc_dt_from', $this->lqc_dt_from, $user->timezone);
            }

            if ($this->lqc_dt_to) {
                QueryHelper::dayEqualByUserTZ($query, 'lqc_dt_to', $this->lqc_dt_to, $user->timezone);
            }

            if ($this->lqc_created_dt) {
                QueryHelper::dayEqualByUserTZ($query, 'lqc_created_dt', $this->lqc_created_dt, $user->timezone);
            }

            if ($this->lqc_weight === '0') {
                $query->andWhere(['lqc_weight' => 0]);
            } else {
                $query->andFilterWhere(['lqc_weight' => $this->lqc_weight]);
            }
        }

//		VarDumper::dump($query->createCommand()->getRawSql());die;

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
            $query->andWhere(['NOT IN', 'l_call_status_id', [Lead::CALL_STATUS_PROCESS, Lead::CALL_STATUS_PREPARE, Lead::CALL_STATUS_QUEUE]]);
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
