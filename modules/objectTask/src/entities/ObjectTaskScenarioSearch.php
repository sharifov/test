<?php

namespace modules\objectTask\src\entities;

use yii\data\ActiveDataProvider;

/**
 * ObjectTaskScenarioSearch represents the model behind the search form of `modules\objectTask\src\entities\ObjectTaskScenario`.
 */
class ObjectTaskScenarioSearch extends ObjectTaskScenario
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ots_id', 'ots_updated_user_id'], 'integer'],
            [['ots_key', 'ots_data_json', 'ots_updated_dt'], 'safe'],
        ];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = ObjectTaskScenario::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');

            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'ots_id' => $this->ots_id,
            'ots_updated_dt' => $this->ots_updated_dt,
            'ots_updated_user_id' => $this->ots_updated_user_id,
        ]);

        $query->andFilterWhere(['like', 'ots_key', $this->ots_key])
            ->andFilterWhere(['like', 'ots_data_json', $this->ots_data_json]);

        return $dataProvider;
    }
}
