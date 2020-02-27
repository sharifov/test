<?php

namespace modules\hotel\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\hotel\models\HotelQuoteRoomPax;

/**
 * HotelQuoteRoomPaxSearch represents the model behind the search form of `modules\hotel\models\HotelQuoteRoomPax`.
 */
class HotelQuoteRoomPaxSearch extends HotelQuoteRoomPax
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['hqrp_id', 'hqrp_hotel_room_id', 'hqrp_type_id', 'hqrp_age'], 'integer'],
            [['hqrp_first_name', 'hqrp_last_name', 'hqrp_dob'], 'safe'],
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
        $query = HotelQuoteRoomPax::find();

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
            'hqrp_id' => $this->hqrp_id,
            'hqrp_hotel_room_id' => $this->hqrp_hotel_room_id,
            'hqrp_type_id' => $this->hqrp_type_id,
            'hqrp_age' => $this->hqrp_age,
            'hqrp_dob' => $this->hqrp_dob,
        ]);

        $query->andFilterWhere(['like', 'hqrp_first_name', $this->hqrp_first_name])
            ->andFilterWhere(['like', 'hqrp_last_name', $this->hqrp_last_name]);

        return $dataProvider;
    }
}
