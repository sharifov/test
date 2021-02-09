<?php

namespace sales\model\airportLang\entity;

use yii\data\ActiveDataProvider;
use yii\db\Expression;

/**
 * Class AirportLangSearch
 */
class AirportLangSearch extends AirportLang
{
    public function rules(): array
    {
        return [
            ['ail_city', 'string', 'max' => 40],
            ['ail_country', 'string', 'max' => 40],
            ['ail_created_dt', 'datetime', 'format' => 'php:Y-m-d'],
            ['ail_updated_dt', 'datetime', 'format' => 'php:Y-m-d'],
            ['ail_created_user_id', 'integer'],
            ['ail_iata', 'string', 'max' => 3],
            ['ail_lang', 'string', 'max' => 2],
            ['ail_name', 'string', 'max' => 255],
            ['ail_updated_user_id', 'integer'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['ail_iata' => SORT_ASC]],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'ail_created_user_id' => $this->ail_created_user_id,
            'ail_updated_user_id' => $this->ail_updated_user_id,
        ]);

        $query->andFilterWhere(['like', 'ail_iata', $this->ail_iata])
            ->andFilterWhere(['like', 'ail_lang', $this->ail_lang])
            ->andFilterWhere(['like', 'ail_name', $this->ail_name])
            ->andFilterWhere(['like', 'ail_city', $this->ail_city])
            ->andFilterWhere(['like', 'ail_country', $this->ail_country]);

        if ($this->ail_created_dt) {
            $query->andWhere(new Expression(
                'DATE(ail_created_dt) = :created_dt',
                [':created_dt' => date('Y-m-d', strtotime($this->ail_created_dt))]
            ));
        }
        if ($this->ail_updated_dt) {
            $query->andWhere(new Expression(
                'DATE(ail_updated_dt) = :updated_dt',
                [':updated_dt' => date('Y-m-d', strtotime($this->ail_updated_dt))]
            ));
        }
        return $dataProvider;
    }
}
