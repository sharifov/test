<?php

namespace modules\shiftSchedule\src\entities\userShiftSchedule\search;

use common\models\Employee;
use modules\shiftSchedule\src\entities\userShiftSchedule\Scopes;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

/**
 * @property string $clientStartDate
 * @property string $clientEndDate
 */
class SearchUserShiftSchedule extends UserShiftSchedule
{
    public string $clientStartDate = '';
    public string $clientEndDate = '';
    public $shiftIds = [];
    public string $startedDateRange = '';
    public string $endedDateRange = '';

    public function rules(): array
    {
        return [
            ['uss_created_dt', 'safe'],

            ['uss_created_user_id', 'integer'],

            ['uss_customized', 'integer'],

            ['uss_description', 'safe'],

            ['uss_duration', 'integer'],

            ['uss_end_utc_dt', 'safe'],

            ['uss_id', 'integer'],

            ['uss_shift_id', 'integer'],

            ['uss_ssr_id', 'integer'],

            ['uss_start_utc_dt', 'safe'],

            ['uss_status_id', 'integer'],

            ['uss_type_id', 'integer'],

            ['uss_updated_dt', 'safe'],

            ['uss_updated_user_id', 'integer'],

            ['uss_user_id', 'integer'],
            ['uss_sst_id', 'integer'],

            ['shiftIds', 'each', 'rule' => ['integer']],
            ['startedDateRange', 'string'],
            [['startedDateRange', 'endedDateRange'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
        ];
    }

    private function filterSearchQuery(Scopes $query): void
    {
        if (!empty($this->shiftIds)) {
            $query->andWhere(['IN', 'uss_shift_id', $this->shiftIds]);
        }

        if (!empty($this->startedDateRange)) {
            $startedRange = explode(' - ', $this->startedDateRange);

            if ($startedRange[0] && $startedRange[1]) {
                $fromDate = date('Y-m-d', strtotime($startedRange[0]));
                $toDate = date('Y-m-d', strtotime($startedRange[1]));
                $query->andWhere(['BETWEEN', 'DATE(uss_start_utc_dt)', $fromDate, $toDate]);
            }
        }

        if (!empty($this->endedDateRange)) {
            $endedRange = explode(' - ', $this->endedDateRange);

            if ($endedRange[0] && $endedRange[1]) {
                $fromDate = date('Y-m-d', strtotime($endedRange[0]));
                $toDate = date('Y-m-d', strtotime($endedRange[1]));
                $query->andWhere(['BETWEEN', 'DATE(uss_end_utc_dt)', $fromDate, $toDate]);
            }
        }

        $query->andFilterWhere([
            'uss_id' => $this->uss_id,
            'uss_user_id' => $this->uss_user_id,
            'uss_shift_id' => $this->uss_shift_id,
            'uss_ssr_id' => $this->uss_ssr_id,
            'uss_sst_id' => $this->uss_sst_id,
            'DATE(uss_start_utc_dt)' => $this->uss_start_utc_dt,
            'DATE(uss_end_utc_dt)' => $this->uss_end_utc_dt,
            'uss_duration' => $this->uss_duration,
            'uss_status_id' => $this->uss_status_id,
            'uss_type_id' => $this->uss_type_id,
            'uss_customized' => $this->uss_customized,
            'date(uss_created_dt)' => $this->uss_created_dt,
            'date(uss_updated_dt)' => $this->uss_updated_dt,
            'uss_created_user_id' => $this->uss_created_user_id,
            'uss_updated_user_id' => $this->uss_updated_user_id,
        ]);

        $query->andFilterWhere(['like', 'uss_description', $this->uss_description]);
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'key' => 'uss_id',
            'sort' => ['defaultOrder' => ['uss_id' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $this->filterSearchQuery($query);

        return $dataProvider;
    }

    /**
     * @param $params
     * @param int $userId
     * @param string|null $startDate
     * @param string|null $endDate
     * @return ActiveDataProvider
     */
    public function searchByUserId(
        $params,
        int $userId,
        ?string $startDate = null,
        ?string $endDate = null
    ): ActiveDataProvider {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['uss_id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 7,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->where(['uss_user_id' => $userId]);

        if (!empty($startDate) && !empty($endDate)) {
            $this->clientStartDate = $startDate;
            $this->clientEndDate = $endDate;

            $startDateTime = Employee::convertTimeFromUserDtToUTC(strtotime($startDate));
            $endDateTime = Employee::convertTimeFromUserDtToUTC(strtotime($endDate));

            $query->andWhere([
                'OR',
                ['between', 'uss_start_utc_dt', $startDateTime, $endDateTime],
                ['between', 'uss_end_utc_dt', $startDateTime, $endDateTime],
                [
                    'AND',
                    ['>=', 'uss_start_utc_dt', $startDateTime],
                    ['<=', 'uss_end_utc_dt', $endDateTime]
                ],
                [
                    'AND',
                    ['<=', 'uss_start_utc_dt', $startDateTime],
                    ['>=', 'uss_end_utc_dt', $endDateTime]
                ]
            ]);
        }


        //CONVERT_TZ(NOW(), 'Asia/Calcutta', 'UTC')

        $query->andFilterWhere([
            'uss_id' => $this->uss_id,
            'uss_shift_id' => $this->uss_shift_id,
            'uss_ssr_id' => $this->uss_ssr_id,
            'uss_sst_id' => $this->uss_sst_id,
            //'DATE(uss_start_utc_dt)' => $this->uss_start_utc_dt,
            //'DATE(uss_end_utc_dt)' => $this->uss_end_utc_dt,
            'uss_duration' => $this->uss_duration,
            'uss_status_id' => $this->uss_status_id,
            'uss_type_id' => $this->uss_type_id
        ]);

        return $dataProvider;
    }

    public function searchIds($params): array
    {
        $query = static::find()->select('uss_id');

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');

            return [];
        }

        $this->filterSearchQuery($query);

        return ArrayHelper::map($query->asArray()->all(), 'uss_id', 'uss_id');
    }
}
