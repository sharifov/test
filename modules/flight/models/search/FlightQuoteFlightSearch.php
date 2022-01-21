<?php

namespace modules\flight\models\search;

use modules\flight\models\FlightQuote;
use modules\order\src\entities\order\Order;
use modules\product\src\entities\productQuote\ProductQuote;
use yii\data\ActiveDataProvider;
use modules\flight\models\FlightQuoteFlight;
use yii\helpers\ArrayHelper;

class FlightQuoteFlightSearch extends FlightQuoteFlight
{
    public $orderId;

    public function rules(): array
    {
        return [
            [['fqf_booking_id', 'fqf_child_booking_id'], 'string', 'max' => 50],
            ['fqf_main_airline', 'string', 'max' => 2],
            ['fqf_pnr', 'string', 'max' => 70],
            ['fqf_original_data_json', 'string'],
            ['fqf_validating_carrier', 'string', 'max' => 2],

            [['fqf_fq_id', 'fqf_id', 'fqf_status_id', 'fqf_trip_type_id', 'orderId'], 'integer'],

            [['fqf_created_dt', 'fqf_updated_dt'], 'datetime', 'format' => 'php:Y-m-d'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $query->alias('fqf');

        $query->addSelect(['fqf.*', 'or_id as orderId']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['fqf_id' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->leftJoin(FlightQuote::tableName(), 'fq_id = fqf_fq_id');
        $query->leftJoin(ProductQuote::tableName(), 'pq_id = fq_product_quote_id');
        $query->leftJoin(Order::tableName(), 'or_id = pq_order_id');

        $query->andFilterWhere([
            'fqf_id' => $this->fqf_id,
            'fqf_fq_id' => $this->fqf_fq_id,
            'fqf_trip_type_id' => $this->fqf_trip_type_id,
            'fqf_status_id' => $this->fqf_status_id,
            'DATE(fqf_created_dt)' => $this->fqf_created_dt,
            'DATE(fqf_updated_dt)' => $this->fqf_updated_dt,
            'or_id' => $this->orderId
        ]);

        $query
            ->andFilterWhere(['like', 'fqf_main_airline', $this->fqf_main_airline])
            ->andFilterWhere(['like', 'fqf_booking_id', $this->fqf_booking_id])
            ->andFilterWhere(['like', 'fqf_child_booking_id', $this->fqf_child_booking_id])
            ->andFilterWhere(['like', 'fqf_pnr', $this->fqf_pnr])
            ->andFilterWhere(['like', 'fqf_validating_carrier', $this->fqf_validating_carrier])
            ->andFilterWhere(['like', 'fqf_original_data_json', $this->fqf_original_data_json]);

        return $dataProvider;
    }
}
