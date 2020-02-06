<?php

namespace modules\order\src\entities\order\search;

use yii\data\ActiveDataProvider;
use modules\order\src\entities\order\Order;

class OrderSearch extends Order
{
    public function rules(): array
    {
        return [
            [['or_id', 'or_lead_id', 'or_status_id', 'or_pay_status_id', 'or_owner_user_id', 'or_created_user_id', 'or_updated_user_id'], 'integer'],
            [['or_gid', 'or_uid', 'or_name', 'or_description', 'or_client_currency'], 'safe'],
            [['or_app_total', 'or_app_markup', 'or_agent_markup', 'or_client_total', 'or_client_currency_rate'], 'number'],

            ['or_created_dt', 'date', 'format' => 'php:Y-m-d'],
            ['or_updated_dt', 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public function searchByLead(int $leadId): ActiveDataProvider
    {
        $query = self::find()->andWhere(['or_lead_id' => $leadId]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $dataProvider;
    }
}
