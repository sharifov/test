<?php

namespace src\model\leadUserRating\entity;

use yii\data\ActiveDataProvider;

class LeadUserRatingSearch extends LeadUserRating
{
    public function rules(): array
    {
        return [
            ['lur_created_dt', 'date', 'format' => 'php:Y-m-d'],
            ['lur_updated_dt', 'date', 'format' => 'php:Y-m-d'],

            [['lur_rating'], 'in', 'range' => array_keys(LeadUserRating::getRatingList())],

            ['lur_lead_id', 'integer'],

            ['lur_user_id', 'integer'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['lur_lead_id' => SORT_ASC]],
            'pagination' => ['pageSize' => 30],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'lur_lead_id' => $this->lur_lead_id,
            'lur_user_id' => $this->lur_user_id,
            'lur_rating' => $this->lur_rating,
            'DATE(lur_updated_dt)' => $this->lur_updated_dt,
            'DATE(lur_created_dt)' => $this->lur_created_dt,
        ]);

        return $dataProvider;
    }
}
