<?php

namespace modules\shiftSchedule\src\entities\shiftScheduleRequest\search;

use common\models\Employee;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;
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
            [['ssr_id', 'ssr_uss_id', 'ssr_sst_id', 'ssr_status_id', 'ssr_created_user_id', 'ssr_updated_user_id'], 'integer'],
            [['ssr_description', 'ssr_created_dt', 'ssr_updated_dt', 'clientStartDate', 'clientEndDate'], 'safe'],
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
            'sort' => [
                'defaultOrder' => ['ssr_created_dt' => SORT_DESC],
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
            'ssr_id' => $this->ssr_id,
            'ssr_uss_id' => $this->ssr_uss_id,
            'ssr_sst_id' => $this->ssr_sst_id,
            'ssr_status_id' => $this->ssr_status_id,
            'DATE(ssr_created_dt)' => $this->ssr_created_dt,
            'ssr_updated_dt' => $this->ssr_updated_dt,
            'ssr_created_user_id' => $this->ssr_created_user_id,
            'ssr_updated_user_id' => $this->ssr_updated_user_id,
        ]);

        $query->andFilterWhere(['like', 'ssr_description', $this->ssr_description]);

        return $dataProvider;
    }

    /**
     * @param array $params
     * @param ActiveQuery|array $userList
     * @param string|null $startDate
     * @param string|null $endDate
     * @return ActiveDataProvider
     */
    public function searchByUsers(array $params, $userList, string $startDate = null, string $endDate = null): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['ssr_id' => SORT_DESC]],
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
            'sort' => ['defaultOrder' => ['ssr_id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 7,
            ],
        ]);
    }

    /**
     * @param ActiveQuery|array $userList
     * @param ShiftScheduleRequestSearch|null $model
     * @param string|null $startDate
     * @param string|null $endDate
     * @return ActiveQuery
     */
    public static function getSearchQuery($userList, ShiftScheduleRequestSearch $model = null, ?string $startDate = null, ?string $endDate = null): ActiveQuery
    {
        $query = ShiftScheduleRequestSearch::find();
        $query->where(['IS NOT', 'ssr_uss_id', null]);
        if ($model->ssr_created_user_id ?? false) {
            $query->andWhere(['ssr_created_user_id' => $model->ssr_created_user_id]);
        } else {
            $query->andWhere(['ssr_created_user_id' => $userList]);
        }
        if (!empty($startDate) && !empty($endDate)) {
            $startDateTime = Employee::convertTimeFromUserDtToUTC(strtotime($startDate));
            $endDateTime = Employee::convertTimeFromUserDtToUTC(strtotime($endDate));
        } else {
            $startDateTime = $model->clientStartDate ?? '';
            $endDateTime = $model->clientEndDate ?? '';
        }

        if (!empty($startDateTime) && !empty($endDateTime)) {
            $query->joinWith('srhUss');
            $query->andWhere([
                'OR',
                ['between', UserShiftSchedule::tableName() . '.uss_start_utc_dt', $startDateTime, $endDateTime],
                ['between', UserShiftSchedule::tableName() . '.uss_end_utc_dt', $startDateTime, $endDateTime],
                [
                    'AND',
                    ['>=', UserShiftSchedule::tableName() . '.uss_start_utc_dt', $startDateTime],
                    ['<=', UserShiftSchedule::tableName() . '.uss_end_utc_dt', $endDateTime]
                ],
                [
                    'AND',
                    ['<=', UserShiftSchedule::tableName() . '.uss_start_utc_dt', $startDateTime],
                    ['>=', UserShiftSchedule::tableName() . '.uss_end_utc_dt', $endDateTime]
                ]
            ]);
        }

        $query->select(['ssr_id' => 'MAX(ssr_id)', 'ssr_uss_id'])
            ->groupBy(['ssr_uss_id']);

        $query = static::find()
            ->from($query)
            ->select('ssr_id');

        $queryResult = static::find()
            ->where(['in', 'ssr_id', $query]);

        if (!empty($model)) {
            $queryResult->andFilterWhere([
                'ssr_id' => $model->ssr_id,
                'ssr_uss_id' => $model->ssr_uss_id,
                'ssr_sst_id' => $model->ssr_sst_id,
                'ssr_status_id' => $model->ssr_status_id,
                'DATE(ssr_created_dt)' => $model->ssr_created_dt,
                'ssr_updated_dt' => $model->ssr_updated_dt,
                'ssr_updated_user_id' => $model->ssr_updated_user_id,
            ]);

            $queryResult->andFilterWhere(['like', 'ssr_description', $model->ssr_description]);
        }

        return $queryResult;
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
