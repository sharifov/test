<?php

namespace modules\flight\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\flight\models\Flight;

/**
 * FlightSearch represents the model behind the search form of `modules\flight\models\Flight`.
 */
class FlightSearch extends Flight
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fl_id', 'fl_product_id', 'fl_trip_type_id', 'fl_adults', 'fl_children', 'fl_infants'], 'integer'],
            [['fl_cabin_class'], 'safe'],
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
        $query = Flight::find();

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
            'fl_id' => $this->fl_id,
            'fl_product_id' => $this->fl_product_id,
            'fl_trip_type_id' => $this->fl_trip_type_id,
            'fl_adults' => $this->fl_adults,
            'fl_children' => $this->fl_children,
            'fl_infants' => $this->fl_infants,
        ]);

        $query->andFilterWhere(['like', 'fl_cabin_class', $this->fl_cabin_class]);

        return $dataProvider;
    }
}
