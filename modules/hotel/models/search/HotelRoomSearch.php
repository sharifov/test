<?php

namespace modules\hotel\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\hotel\models\HotelRoom;

/**
 * HotelRoomSearch represents the model behind the search form of `modules\hotel\models\HotelRoom`.
 */
class HotelRoomSearch extends HotelRoom
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['hr_id', 'hr_hotel_id'], 'integer'],
            [['hr_room_name'], 'safe'],
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
        $query = HotelRoom::find();

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
            'hr_id' => $this->hr_id,
            'hr_hotel_id' => $this->hr_hotel_id,
        ]);

        $query->andFilterWhere(['like', 'hr_room_name', $this->hr_room_name]);

        return $dataProvider;
    }
}
