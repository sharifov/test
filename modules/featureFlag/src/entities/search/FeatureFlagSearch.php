<?php

namespace modules\featureFlag\src\entities\search;

use yii\data\ActiveDataProvider;
use modules\featureFlag\src\entities\FeatureFlag;

class FeatureFlagSearch extends FeatureFlag
{
    public function rules(): array
    {
        return [
            ['ff_attributes', 'safe'],

            ['ff_category', 'safe'],

            ['ff_condition', 'safe'],

            ['ff_description', 'safe'],

            ['ff_enable_type', 'integer'],

            ['ff_id', 'integer'],

            ['ff_key', 'safe'],

            ['ff_name', 'safe'],

            ['ff_type', 'safe'],

            ['ff_updated_dt', 'safe'],

            ['ff_updated_user_id', 'integer'],

            ['ff_value', 'safe'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['ff_id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'ff_id' => $this->ff_id,
            'ff_enable_type' => $this->ff_enable_type,
            'ff_updated_dt' => $this->ff_updated_dt,
            'ff_updated_user_id' => $this->ff_updated_user_id,
        ]);

        $query->andFilterWhere(['like', 'ff_key', $this->ff_key])
            ->andFilterWhere(['like', 'ff_name', $this->ff_name])
            ->andFilterWhere(['like', 'ff_type', $this->ff_type])
            ->andFilterWhere(['like', 'ff_value', $this->ff_value])
            ->andFilterWhere(['like', 'ff_category', $this->ff_category])
            ->andFilterWhere(['like', 'ff_description', $this->ff_description])
            ->andFilterWhere(['like', 'ff_attributes', $this->ff_attributes])
            ->andFilterWhere(['like', 'ff_condition', $this->ff_condition]);

        return $dataProvider;
    }
}
