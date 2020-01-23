<?php

namespace modules\hotel\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\hotel\models\Hotel;

/**
 * HotelSearch represents the model behind the search form of `modules\hotel\models\Hotel`.
 */
class HotelSearch extends Hotel
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ph_id', 'ph_product_id', 'ph_min_star_rate', 'ph_max_star_rate', 'ph_max_price_rate', 'ph_min_price_rate', 'ph_zone_code', 'ph_hotel_code'], 'integer'],
            [['ph_request_hash_key'], 'string'],
            [['ph_check_in_date', 'ph_check_out_date', 'ph_destination_code', 'ph_destination_label'], 'safe'],
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
        $query = Hotel::find();

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
            'ph_id' => $this->ph_id,
            'ph_product_id' => $this->ph_product_id,
            'ph_check_in_date' => $this->ph_check_in_date,
            'ph_check_out_date' => $this->ph_check_out_date,
            'ph_min_star_rate' => $this->ph_min_star_rate,
            'ph_max_star_rate' => $this->ph_max_star_rate,
            'ph_max_price_rate' => $this->ph_max_price_rate,
            'ph_min_price_rate' => $this->ph_min_price_rate,
			'ph_zone_code' => $this->ph_zone_code,
			'ph_hotel_code' => $this->ph_hotel_code,
            'ph_request_hash_key' => $this->ph_request_hash_key
        ]);

        $query->andFilterWhere(['like', 'ph_destination_code', $this->ph_destination_code]);
        $query->andFilterWhere(['like', 'ph_destination_label', $this->ph_destination_label]);

        return $dataProvider;
    }
}
