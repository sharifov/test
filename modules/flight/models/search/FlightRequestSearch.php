<?php

namespace modules\flight\models\search;

use yii\data\ActiveDataProvider;
use modules\flight\models\FlightRequest;

class FlightRequestSearch extends FlightRequest
{
    public function rules(): array
    {
        return [
            ['fr_created_api_user_id', 'integer'],

            ['fr_data_json', 'safe'],

            ['fr_hash', 'safe'],

            ['fr_id', 'integer'],

            ['fr_job_id', 'integer'],

            ['fr_month', 'integer'],

            ['fr_status_id', 'integer'],

            ['fr_type_id', 'integer'],

            ['fr_year', 'integer'],

            [['fr_created_dt', 'fr_updated_dt'], 'date', 'format' => 'php:Y-m-d'],

            ['fr_booking_id', 'safe'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['fr_id' => SORT_DESC]],
            'pagination' => ['pageSize' => 30],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'fr_id' => $this->fr_id,
            'fr_type_id' => $this->fr_type_id,
            'fr_created_api_user_id' => $this->fr_created_api_user_id,
            'fr_status_id' => $this->fr_status_id,
            'fr_job_id' => $this->fr_job_id,
            'DATE(fr_created_dt)' => $this->fr_created_dt,
            'DATE(fr_updated_dt)' => $this->fr_updated_dt,
            'fr_year' => $this->fr_year,
            'fr_month' => $this->fr_month,
        ]);

        $query->andFilterWhere(['like', 'fr_hash', $this->fr_hash])
            ->andFilterWhere(['like', 'fr_booking_id', $this->fr_booking_id])
            ->andFilterWhere(['like', 'fr_data_json', $this->fr_data_json]);

        return $dataProvider;
    }
}
