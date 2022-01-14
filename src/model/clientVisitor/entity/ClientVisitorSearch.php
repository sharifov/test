<?php

namespace src\model\clientVisitor\entity;

use yii\data\ActiveDataProvider;
use src\model\clientVisitor\entity\ClientVisitor;

class ClientVisitorSearch extends ClientVisitor
{
    public function rules(): array
    {
        return [
            ['cv_client_id', 'integer'],

            ['cv_created_dt', 'date', 'format' => 'php:Y-m-d'],

            ['cv_id', 'integer'],

            ['cv_visitor_id', 'string', 'max' => 50],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['cv_id' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'cv_id' => $this->cv_id,
            'cv_client_id' => $this->cv_client_id,
            'DATE(cv_created_dt)' => $this->cv_created_dt,
        ]);

        $query->andFilterWhere(['like', 'cv_visitor_id', $this->cv_visitor_id]);

        return $dataProvider;
    }
}
