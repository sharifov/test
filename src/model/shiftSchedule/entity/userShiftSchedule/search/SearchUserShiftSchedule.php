<?php

namespace src\model\shiftSchedule\entity\userShiftSchedule\search;

use common\models\Employee;
use yii\data\ActiveDataProvider;
use src\model\shiftSchedule\entity\userShiftSchedule\UserShiftSchedule;

/**
 * @property string $clientStartDate
 * @property string $clientEndDate
 */
class SearchUserShiftSchedule extends UserShiftSchedule
{
    public string $clientStartDate = '';
    public string $clientEndDate = '';

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
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // $query->where('0=1');
            return $dataProvider;
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
}
