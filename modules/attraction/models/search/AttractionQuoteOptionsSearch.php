<?php

namespace modules\attraction\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\attraction\models\AttractionQuoteOptions;

/**
 * AttractionQuoteOptionsSearch represents the model behind the search form of `modules\attraction\models\AttractionQuoteOptions`.
 */
class AttractionQuoteOptionsSearch extends AttractionQuoteOptions
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['atqo_id', 'atqo_attraction_quote_id', 'atqo_is_answered'], 'integer'],
            [['atqo_answered_value', 'atqo_label', 'atqo_answer_formatted_text'], 'safe'],
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
        $query = AttractionQuoteOptions::find();

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
            'atqo_id' => $this->atqo_id,
            'atqo_attraction_quote_id' => $this->atqo_attraction_quote_id,
            'atqo_is_answered' => $this->atqo_is_answered,
        ]);

        $query->andFilterWhere(['like', 'atqo_answered_value', $this->atqo_answered_value])
            ->andFilterWhere(['like', 'atqo_label', $this->atqo_label])
            ->andFilterWhere(['like', 'atqo_answer_formatted_text', $this->atqo_answer_formatted_text]);

        return $dataProvider;
    }
}
