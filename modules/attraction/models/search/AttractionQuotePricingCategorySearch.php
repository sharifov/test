<?php

namespace modules\attraction\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\attraction\models\AttractionQuotePricingCategory;

/**
 * AttractionQuotePricingCategorySearch represents the model behind the search form of `modules\attraction\models\AttractionQuotePricingCategory`.
 */
class AttractionQuotePricingCategorySearch extends AttractionQuotePricingCategory
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['atqpc_id', 'atqpc_attraction_quote_id', 'atqpc_min_age', 'atqpc_max_age', 'atqpc_min_participants', 'atqpc_max_participants', 'atqpc_quantity'], 'integer'],
            [['atqpc_category_id', 'atqpc_label', 'atqpc_currency'], 'safe'],
            [['atqpc_price', 'atqpc_system_mark_up', 'atqpc_agent_mark_up'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
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
    public function search($params)
    {
        $query = AttractionQuotePricingCategory::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'atqpc_id' => $this->atqpc_id,
            'atqpc_attraction_quote_id' => $this->atqpc_attraction_quote_id,
            'atqpc_min_age' => $this->atqpc_min_age,
            'atqpc_max_age' => $this->atqpc_max_age,
            'atqpc_min_participants' => $this->atqpc_min_participants,
            'atqpc_max_participants' => $this->atqpc_max_participants,
            'atqpc_quantity' => $this->atqpc_quantity,
            'atqpc_price' => $this->atqpc_price,
            'atqpc_system_mark_up' => $this->atqpc_system_mark_up,
            'atqpc_agent_mark_up' => $this->atqpc_agent_mark_up,
        ]);

        $query->andFilterWhere(['like', 'atqpc_category_id', $this->atqpc_category_id])
            ->andFilterWhere(['like', 'atqpc_label', $this->atqpc_label])
            ->andFilterWhere(['like', 'atqpc_currency', $this->atqpc_currency]);

        return $dataProvider;
    }
}
