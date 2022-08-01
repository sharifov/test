<?php

namespace modules\smartLeadDistribution\src\entities;

use yii\data\ActiveDataProvider;

class LeadRatingParameterSearch extends LeadRatingParameter
{
    public function rules(): array
    {
        return [
            [['lrp_id', 'lrp_point'], 'integer'],
            [['lrp_object'], 'string'],
            [['lrp_condition_json'], 'safe'],
        ];
    }

    public function search(array $params)
    {
        $query = LeadRatingParameter::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => [
                'lrp_attribute' => SORT_DESC,
                'lrp_point' => SORT_DESC,
            ]],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');

            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'lrp_id' => $this->lrp_id,
            'lrp_point' => $this->lrp_point,
            'lrp_object' => $this->lrp_object
        ]);

        $query->andFilterWhere(['like', 'lrp_condition_json', $this->lrp_condition_json]);

        return $dataProvider;
    }
}
