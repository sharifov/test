<?php

namespace modules\hotel\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\hotel\models\HotelRoomPax;

/**
 * HotelRoomPaxSearch represents the model behind the search form of `modules\hotel\models\HotelRoomPax`.
 */
class HotelRoomPaxSearch extends HotelRoomPax
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['hrp_id', 'hrp_hotel_room_id', 'hrp_type_id', 'hrp_age'], 'integer'],
            [['hrp_first_name', 'hrp_last_name', 'hrp_dob'], 'safe'],
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
        $query = HotelRoomPax::find();

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
            'hrp_id' => $this->hrp_id,
            'hrp_hotel_room_id' => $this->hrp_hotel_room_id,
            'hrp_type_id' => $this->hrp_type_id,
            'hrp_age' => $this->hrp_age,
            'hrp_dob' => $this->hrp_dob,
        ]);

        $query->andFilterWhere(['like', 'hrp_first_name', $this->hrp_first_name])
            ->andFilterWhere(['like', 'hrp_last_name', $this->hrp_last_name]);

        return $dataProvider;
    }
}
