<?php

namespace modules\order\src\entities\order\search;

use common\models\Employee;
use modules\flight\models\FlightQuote;
use modules\flight\models\FlightQuoteFlight;
use modules\product\src\entities\productQuote\ProductQuote;
use src\helpers\query\QueryHelper;
use yii\data\ActiveDataProvider;
use modules\order\src\entities\order\Order;
use yii\db\ActiveQuery;
use yii\db\Query;

class OrderCrudSearch extends Order
{
    public $bookingIds;

    public function rules(): array
    {
        return [
            [['or_id', 'or_lead_id', 'or_status_id', 'or_pay_status_id', 'or_owner_user_id',
                'or_created_user_id', 'or_updated_user_id', 'or_project_id', 'or_type_id', 'or_sale_id'], 'integer'],
            [['or_gid', 'or_uid', 'or_name', 'or_description', 'or_client_currency', 'or_fare_id', 'bookingIds'], 'safe'],
            [['or_app_total', 'or_app_markup', 'or_agent_markup', 'or_client_total', 'or_client_currency_rate', 'or_profit_amount'], 'number'],

            ['or_created_dt', 'date', 'format' => 'php:Y-m-d'],
            ['or_updated_dt', 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public function search($params, Employee $user): ActiveDataProvider
    {
        $query = self::find()->with(['orLead', 'orOwnerUser', 'orCreatedUser', 'orUpdatedUser']);
        $query->alias('o');

        $query->addSelect(["group_concat(tt.fqf_booking_id separator ', ') as bookingIds"]);
        $query->addSelect(['o.*']);

        $subQuery = ProductQuote::find()
            ->select(['fqf_booking_id', 'pq_order_id'])
            ->innerJoin(FlightQuote::tableName(), 'pq_id = fq_product_quote_id')
            ->innerJoin(FlightQuoteFlight::tableName(), 'fq_id = fqf_fq_id');

        $query->leftJoin('(' . $subQuery->createCommand()->rawSql . ') as tt', 'tt.pq_order_id = or_id');

        $query->groupBy('or_id');

        // add conditions that should always apply here

        $mainQuery = self::find()->from($query);

        $dataProvider = new ActiveDataProvider([
            'query' => $mainQuery,
            'sort' => [
                'defaultOrder' => [
                    'or_id' => SORT_DESC,
                ]
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $mainQuery->where('0=1');
            return $dataProvider;
        }

        if ($this->or_created_dt) {
            QueryHelper::dayEqualByUserTZ($mainQuery, 'or_created_dt', $this->or_created_dt, $user->timezone);
        }

        if ($this->or_updated_dt) {
            QueryHelper::dayEqualByUserTZ($mainQuery, 'or_updated_dt', $this->or_updated_dt, $user->timezone);
        }

        // grid filtering conditions
        $mainQuery->andFilterWhere([
            'or_id' => $this->or_id,
            'or_lead_id' => $this->or_lead_id,
            'or_status_id' => $this->or_status_id,
            'or_pay_status_id' => $this->or_pay_status_id,
            'or_app_total' => $this->or_app_total,
            'or_app_markup' => $this->or_app_markup,
            'or_agent_markup' => $this->or_agent_markup,
            'or_client_total' => $this->or_client_total,
            'or_client_currency_rate' => $this->or_client_currency_rate,
            'or_owner_user_id' => $this->or_owner_user_id,
            'or_created_user_id' => $this->or_created_user_id,
            'or_updated_user_id' => $this->or_updated_user_id,
            'or_profit_amount' => $this->or_profit_amount,
            'or_project_id' => $this->or_project_id,
            'or_type_id' => $this->or_type_id,
            'or_sale_id' => $this->or_sale_id,
        ]);

        $mainQuery->andFilterWhere(['like', 'or_gid', $this->or_gid])
            ->andFilterWhere(['like', 'or_uid', $this->or_uid])
            ->andFilterWhere(['like', 'or_name', $this->or_name])
            ->andFilterWhere(['like', 'or_description', $this->or_description])
            ->andFilterWhere(['like', 'or_fare_id', $this->or_fare_id])
            ->andFilterWhere(['like', 'bookingIds', $this->bookingIds])
            ->andFilterWhere(['like', 'or_client_currency', $this->or_client_currency]);

        return $dataProvider;
    }
}
