<?php

namespace modules\hotel\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\hotel\models\HotelList;

/**
 * HotelListSearch represents the model behind the search form of `modules\hotel\models\HotelList`.
 */
class HotelListSearch extends HotelList
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['hl_id', 'hl_code', 'hl_zone_code', 'hl_ranking'], 'integer'],
            [['hl_hash_key', 'hl_name', 'hl_star', 'hl_category_name', 'hl_destination_code', 'hl_destination_name', 'hl_zone_name', 'hl_country_code', 'hl_state_code', 'hl_description', 'hl_address', 'hl_postal_code', 'hl_city', 'hl_email', 'hl_web', 'hl_phone_list', 'hl_image_list', 'hl_image_base_url', 'hl_board_codes', 'hl_segment_codes', 'hl_service_type', 'hl_last_update', 'hl_created_dt', 'hl_updated_dt'], 'safe'],
            [['hl_latitude', 'hl_longitude'], 'number'],
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
        $query = HotelList::find();

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
            'hl_id' => $this->hl_id,
            'hl_code' => $this->hl_code,
            'hl_zone_code' => $this->hl_zone_code,
            'hl_latitude' => $this->hl_latitude,
            'hl_longitude' => $this->hl_longitude,
            'hl_ranking' => $this->hl_ranking,
            'hl_last_update' => $this->hl_last_update,
            'hl_created_dt' => $this->hl_created_dt,
            'hl_updated_dt' => $this->hl_updated_dt,
        ]);

        $query->andFilterWhere(['like', 'hl_hash_key', $this->hl_hash_key])
            ->andFilterWhere(['like', 'hl_name', $this->hl_name])
            ->andFilterWhere(['like', 'hl_star', $this->hl_star])
            ->andFilterWhere(['like', 'hl_category_name', $this->hl_category_name])
            ->andFilterWhere(['like', 'hl_destination_code', $this->hl_destination_code])
            ->andFilterWhere(['like', 'hl_destination_name', $this->hl_destination_name])
            ->andFilterWhere(['like', 'hl_zone_name', $this->hl_zone_name])
            ->andFilterWhere(['like', 'hl_country_code', $this->hl_country_code])
            ->andFilterWhere(['like', 'hl_state_code', $this->hl_state_code])
            ->andFilterWhere(['like', 'hl_description', $this->hl_description])
            ->andFilterWhere(['like', 'hl_address', $this->hl_address])
            ->andFilterWhere(['like', 'hl_postal_code', $this->hl_postal_code])
            ->andFilterWhere(['like', 'hl_city', $this->hl_city])
            ->andFilterWhere(['like', 'hl_email', $this->hl_email])
            ->andFilterWhere(['like', 'hl_web', $this->hl_web])
            ->andFilterWhere(['like', 'hl_phone_list', $this->hl_phone_list])
            ->andFilterWhere(['like', 'hl_image_list', $this->hl_image_list])
            ->andFilterWhere(['like', 'hl_image_base_url', $this->hl_image_base_url])
            ->andFilterWhere(['like', 'hl_board_codes', $this->hl_board_codes])
            ->andFilterWhere(['like', 'hl_segment_codes', $this->hl_segment_codes])
            ->andFilterWhere(['like', 'hl_service_type', $this->hl_service_type]);

        return $dataProvider;
    }
}
