<?php

namespace modules\attraction\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\attraction\models\AttractionQuote;

/**
 * AttractionQuoteSearch represents the model behind the search form of `modules\attraction\models\AttractionQuote`.
 */
class AttractionQuoteSearch extends AttractionQuote
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['atnq_id','atnq_attraction_id', 'atnq_product_quote_id'], 'integer'],
            [['atnq_json_response'], 'safe'],
            [['atnq_hash_key'], 'string', 'max' => 32],
            [['atnq_hash_key'], 'safe'],
            [['atnq_date'], 'safe'],
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
        $query = AttractionQuote::find();

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
            'atnq_id' => $this->atnq_id,
            'atnq_attraction_id' => $this->atnq_attraction_id,
            'atnq_product_quote_id' => $this->atnq_product_quote_id,
            'atnq_date' => $this->atnq_date,
            //'atnq_request_hash' => $this->atnq_request_hash,
        ]);

        $query->andFilterWhere(['like', 'atnq_hash_key', $this->atnq_hash_key]);
            //->andFilterWhere(['like', 'hq_destination_name', $this->hq_destination_name])
            //->andFilterWhere(['like', 'hq_hotel_name', $this->hq_hotel_name]);

        return $dataProvider;
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function searchProduct($params): ActiveDataProvider
    {
        $query = AttractionQuote::find();

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
            'atnq_id' => $this->atnq_id,
            'atnq_attraction_id' => $this->atnq_attraction_id,
            'atnq_product_quote_id' => $this->atnq_product_quote_id,
        ]);

        $query->innerJoinWith('atnqProductQuote')->with('atnqProductQuote');

//        $query->andFilterWhere(['like', 'hq_hash_key', $this->hq_hash_key])
//            ->andFilterWhere(['like', 'hq_destination_name', $this->hq_destination_name])
//            ->andFilterWhere(['like', 'hq_hotel_name', $this->hq_hotel_name]);

        return $dataProvider;
    }
}
