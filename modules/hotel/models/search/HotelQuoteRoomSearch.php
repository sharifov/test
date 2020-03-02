<?php

namespace modules\hotel\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\hotel\models\HotelQuoteRoom;

/**
 * HotelQuoteRoomSearch represents the model behind the search form of `modules\hotel\models\HotelQuoteRoom`.
 */
class HotelQuoteRoomSearch extends HotelQuoteRoom
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['hqr_id', 'hqr_hotel_quote_id', 'hqr_code', 'hqr_rooms', 'hqr_adults', 'hqr_children'], 'integer'],
            [['hqr_room_name', 'hqr_key', 'hqr_class', 'hqr_currency', 'hqr_cancel_from_dt', 'hqr_payment_type', 'hqr_board_code', 'hqr_board_name'], 'safe'],
            [['hqr_amount', 'hqr_cancel_amount', 'hqr_service_fee_percent'], 'number'],
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
        $query = HotelQuoteRoom::find();

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
            'hqr_id' => $this->hqr_id,
            'hqr_hotel_quote_id' => $this->hqr_hotel_quote_id,
            'hqr_code' => $this->hqr_code,
            'hqr_amount' => $this->hqr_amount,
            'hqr_cancel_amount' => $this->hqr_cancel_amount,
            'hqr_cancel_from_dt' => $this->hqr_cancel_from_dt,
            'hqr_rooms' => $this->hqr_rooms,
            'hqr_adults' => $this->hqr_adults,
            'hqr_children' => $this->hqr_children,
            'hqr_service_fee_percent' => $this->hqr_service_fee_percent,
        ]);

        $query->andFilterWhere(['like', 'hqr_room_name', $this->hqr_room_name])
            ->andFilterWhere(['like', 'hqr_key', $this->hqr_key])
            ->andFilterWhere(['like', 'hqr_class', $this->hqr_class])
            ->andFilterWhere(['like', 'hqr_currency', $this->hqr_currency])
            ->andFilterWhere(['like', 'hqr_payment_type', $this->hqr_payment_type])
            ->andFilterWhere(['like', 'hqr_board_code', $this->hqr_board_code])
            ->andFilterWhere(['like', 'hqr_board_name', $this->hqr_board_name]);

        return $dataProvider;
    }
}
