<?php

namespace modules\shiftSchedule\src\entities\shiftScheduleRequest\search;

use common\models\Employee;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\shiftSchedule\src\entities\shiftScheduleRequest\ShiftScheduleRequest;
use yii\db\ActiveQuery;

/**
 * ShiftScheduleRequestSearch represents the model behind the search form of `modules\shiftSchedule\src\entities\shiftScheduleRequest\ShiftScheduleRequest`.
 */
class ShiftScheduleRequestSearch extends ShiftScheduleRequest
{
    public string $clientStartDate = '';
    public string $clientEndDate = '';

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['srh_id', 'srh_uss_id', 'srh_sst_id', 'srh_status_id', 'srh_created_user_id', 'srh_updated_user_id'], 'integer'],
            [['srh_description', 'srh_created_dt', 'srh_update_dt'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios(): array
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
    public function search(array $params): ActiveDataProvider
    {
        $query = ShiftScheduleRequest::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'srh_id' => $this->srh_id,
            'srh_uss_id' => $this->srh_uss_id,
            'srh_sst_id' => $this->srh_sst_id,
            'srh_status_id' => $this->srh_status_id,
            'srh_start_utc_dt' => $this->srh_start_utc_dt,
            'srh_end_utc_dt' => $this->srh_end_utc_dt,
            'srh_created_dt' => $this->srh_created_dt,
            'srh_update_dt' => $this->srh_update_dt,
            'srh_created_user_id' => $this->srh_created_user_id,
            'srh_updated_user_id' => $this->srh_updated_user_id,
        ]);

        $query->andFilterWhere(['like', 'srh_description', $this->srh_description]);

        return $dataProvider;
    }

    /**
     * @param array $params
     * @param ActiveQuery $userList
     * @param string|null $startDate
     * @param string|null $endDate
     * @return ActiveDataProvider
     */
    public function searchByUsers(array $params, ActiveQuery $userList, string $startDate = null, string $endDate = null): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['srh_id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 7,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // $query->where('0=1');
            return $dataProvider;
        }

        if (!empty($startDate)) {
            $this->clientStartDate = date('Y-m-d', strtotime($startDate));
        }
        if (!empty($endDate)) {
            $this->clientEndDate = date('Y-m-d', strtotime($endDate));
        }

        return new ActiveDataProvider([
            'query' => self::getSearchQuery($userList, $this, $startDate, $endDate),
            'sort' => ['defaultOrder' => ['srh_id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 7,
            ],
        ]);
    }

    /**
     * @param ActiveQuery $userList
     * @param ShiftScheduleRequestSearch|null $model
     * @param string|null $startDate
     * @param string|null $endDate
     * @return ActiveQuery
     */
    public static function getSearchQuery(ActiveQuery $userList, ShiftScheduleRequestSearch $model = null, ?string $startDate = null, ?string $endDate = null): ActiveQuery
    {
        $query = ShiftScheduleRequestSearch::find();
        $query->where(['IS NOT', 'srh_uss_id', null]);
        $query->andWhere(['srh_created_user_id' => $userList]);
        if (!empty($startDate) && !empty($endDate)) {
            $startDateTime = Employee::convertTimeFromUserDtToUTC(strtotime($startDate));
            $endDateTime = Employee::convertTimeFromUserDtToUTC(strtotime($endDate));
        } else {
            $startDateTime = date('Y-m-d');
            $endDateTime = date('Y-m-d', strtotime(date("Y-m-d", time()) . " + 365 day"));
        }

        $query->andWhere([
            'OR',
            ['between', 'srh_start_utc_dt', $startDateTime, $endDateTime],
            ['between', 'srh_end_utc_dt', $startDateTime, $endDateTime],
            [
                'AND',
                ['>=', 'srh_start_utc_dt', $startDateTime],
                ['<=', 'srh_end_utc_dt', $endDateTime]
            ],
            [
                'AND',
                ['<=', 'srh_start_utc_dt', $startDateTime],
                ['>=', 'srh_end_utc_dt', $endDateTime]
            ]
        ]);

        if (!empty($model)) {
            $query->andFilterWhere([
                'srh_id' => $model->srh_id,
                'srh_uss_id' => $model->srh_uss_id,
                'srh_sst_id' => $model->srh_sst_id,
                'srh_status_id' => $model->srh_status_id,
                'srh_created_dt' => $model->srh_created_dt,
                'srh_update_dt' => $model->srh_update_dt,
                'srh_updated_user_id' => $model->srh_updated_user_id,
            ]);
        }

        $query->select(['srh_id' => 'MAX(srh_id)', 'srh_uss_id'])
            ->groupBy(['srh_uss_id']);

        $query = static::find()
            ->from($query)
            ->select('srh_id');

        return static::find()
            ->where(['in', 'srh_id', $query]);
    }

    /**
     * @param $startDate
     * @param $endDate
     * @return array
     */
    public static function processingDates($startDate, $endDate): array
    {
        if (!empty($startDate) && !empty($endDate)) {
            $startDateTime = Employee::convertTimeFromUserDtToUTC(strtotime($startDate));
            $endDateTime = Employee::convertTimeFromUserDtToUTC(strtotime($endDate));
        } else {
            $startDateTime = date('Y-m-d');
            $endDateTime = date('Y-m-d', strtotime(date("Y-m-d", time()) . " + 365 day"));
        }

        return [
            'startDateTime' => $startDateTime,
            'endDateTime' => $endDateTime,
        ];
    }
}
