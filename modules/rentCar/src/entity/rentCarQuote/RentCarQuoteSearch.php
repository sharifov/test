<?php

namespace modules\rentCar\src\entity\rentCarQuote;

use yii\data\ActiveDataProvider;
use yii\db\Expression;

class RentCarQuoteSearch extends RentCarQuote
{
    public function rules(): array
    {
        return [
            ['rcq_advantages', 'safe'],

            ['rcq_category', 'string', 'max' => 255],

            ['rcq_created_user_id', 'integer'],

            ['rcq_currency', 'string', 'max' => 3],

            ['rcq_days', 'integer'],

            ['rcq_doors', 'string', 'max' => 50],

            ['rcq_drop_of_location', 'string', 'max' => 255],

            [['rcq_hash_key', 'rcq_request_hash_key'], 'string', 'max' => 32],

            ['rcq_image_url', 'string', 'max' => 500],

            ['rcq_json_response', 'safe'],

            ['rcq_model_name', 'string', 'max' => 255],

            ['rcq_options', 'safe'],

            ['rcq_pick_up_location', 'string', 'max' => 255],

            ['rcq_price_per_day', 'number'],

            ['rcq_product_quote_id', 'integer'],

            ['rcq_rent_car_id', 'integer'],

            ['rcq_seats', 'integer'],

            ['rcq_transmission', 'string', 'max' => 255],

            ['rcq_updated_user_id', 'integer'],

            ['rcq_vendor_logo_url', 'string', 'max' => 500],

            ['rcq_vendor_name', 'string', 'max' => 255],

            [['rcq_created_dt', 'rcq_created_dt', 'rcq_pick_up_dt', 'rcq_drop_off_dt'], 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['rcq_id' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'rcq_id' => $this->rcq_id,
            'rcq_rent_car_id' => $this->rcq_rent_car_id,
            'rcq_product_quote_id' => $this->rcq_product_quote_id,
            'rcq_seats' => $this->rcq_seats,
            'rcq_days' => $this->rcq_days,
            'rcq_price_per_day' => $this->rcq_price_per_day,
            'rcq_created_user_id' => $this->rcq_created_user_id,
            'rcq_updated_user_id' => $this->rcq_updated_user_id,
        ]);

        $query->andFilterWhere(['like', 'rcq_hash_key', $this->rcq_hash_key])
            ->andFilterWhere(['like', 'rcq_json_response', $this->rcq_json_response])
            ->andFilterWhere(['like', 'rcq_model_name', $this->rcq_model_name])
            ->andFilterWhere(['like', 'rcq_category', $this->rcq_category])
            ->andFilterWhere(['like', 'rcq_image_url', $this->rcq_image_url])
            ->andFilterWhere(['like', 'rcq_vendor_name', $this->rcq_vendor_name])
            ->andFilterWhere(['like', 'rcq_vendor_logo_url', $this->rcq_vendor_logo_url])
            ->andFilterWhere(['like', 'rcq_transmission', $this->rcq_transmission])
            ->andFilterWhere(['like', 'rcq_doors', $this->rcq_doors])
            ->andFilterWhere(['like', 'rcq_options', $this->rcq_options])
            ->andFilterWhere(['like', 'rcq_currency', $this->rcq_currency])
            ->andFilterWhere(['like', 'rcq_advantages', $this->rcq_advantages])
            ->andFilterWhere(['like', 'rcq_pick_up_location', $this->rcq_pick_up_location])
            ->andFilterWhere(['like', 'rcq_drop_of_location', $this->rcq_drop_of_location])
            ->andFilterWhere(['like', 'rcq_request_hash_key', $this->rcq_request_hash_key]);

        if ($this->rcq_created_dt) {
            $query->andWhere(new Expression(
                'DATE(rcq_created_dt) = :search_date',
                [':search_date' => date('Y-m-d', strtotime($this->rcq_created_dt))]
            ));
        }
        if ($this->rcq_updated_dt) {
            $query->andWhere(new Expression(
                'DATE(rcq_updated_dt) = :search_date',
                [':search_date' => date('Y-m-d', strtotime($this->rcq_updated_dt))]
            ));
        }
        if ($this->rcq_pick_up_dt) {
            $query->andWhere(new Expression(
                'DATE(rcq_pick_up_dt) = :search_date',
                [':search_date' => date('Y-m-d', strtotime($this->rcq_pick_up_dt))]
            ));
        }
        if ($this->rcq_drop_off_dt) {
            $query->andWhere(new Expression(
                'DATE(rcq_drop_off_dt) = :search_date',
                [':search_date' => date('Y-m-d', strtotime($this->rcq_drop_off_dt))]
            ));
        }

        return $dataProvider;
    }

    public function searchProduct($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['rcq_id' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'rcq_id' => $this->rcq_id,
            'rcq_rent_car_id' => $this->rcq_rent_car_id,
            'rcq_product_quote_id' => $this->rcq_product_quote_id,
            'rcq_seats' => $this->rcq_seats,
            'rcq_days' => $this->rcq_days,
            'rcq_price_per_day' => $this->rcq_price_per_day,
            'rcq_created_user_id' => $this->rcq_created_user_id,
            'rcq_updated_user_id' => $this->rcq_updated_user_id,
        ]);

        $query->andFilterWhere(['like', 'rcq_hash_key', $this->rcq_hash_key])
            ->andFilterWhere(['like', 'rcq_json_response', $this->rcq_json_response])
            ->andFilterWhere(['like', 'rcq_model_name', $this->rcq_model_name])
            ->andFilterWhere(['like', 'rcq_category', $this->rcq_category])
            ->andFilterWhere(['like', 'rcq_image_url', $this->rcq_image_url])
            ->andFilterWhere(['like', 'rcq_vendor_name', $this->rcq_vendor_name])
            ->andFilterWhere(['like', 'rcq_vendor_logo_url', $this->rcq_vendor_logo_url])
            ->andFilterWhere(['like', 'rcq_transmission', $this->rcq_transmission])
            ->andFilterWhere(['like', 'rcq_doors', $this->rcq_doors])
            ->andFilterWhere(['like', 'rcq_options', $this->rcq_options])
            ->andFilterWhere(['like', 'rcq_currency', $this->rcq_currency])
            ->andFilterWhere(['like', 'rcq_advantages', $this->rcq_advantages])
            ->andFilterWhere(['like', 'rcq_pick_up_location', $this->rcq_pick_up_location])
            ->andFilterWhere(['like', 'rcq_drop_of_location', $this->rcq_drop_of_location]);

        if ($this->rcq_created_dt) {
            $query->andWhere(new Expression(
                'DATE(rcq_created_dt) = :search_date',
                [':search_date' => date('Y-m-d', strtotime($this->rcq_created_dt))]
            ));
        }
        if ($this->rcq_updated_dt) {
            $query->andWhere(new Expression(
                'DATE(rcq_updated_dt) = :search_date',
                [':search_date' => date('Y-m-d', strtotime($this->rcq_updated_dt))]
            ));
        }

        $query->innerJoinWith('rcqProductQuote')->with('rcqProductQuote');

        return $dataProvider;
    }
}
