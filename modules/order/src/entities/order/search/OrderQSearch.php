<?php

namespace modules\order\src\entities\order\search;

use modules\order\src\entities\order\Order;
use yii\base\BaseObject;
use yii\data\ActiveDataProvider;

class OrderQSearch extends Order
{
    public function rules(): array
    {
        return [
            [['or_id', 'or_lead_id', 'or_status_id', 'or_pay_status_id', 'or_owner_user_id',
                'or_created_user_id', 'or_updated_user_id', 'or_project_id', 'or_type_id'], 'integer'],
            [['or_gid', 'or_uid', 'or_fare_id', 'or_name', 'or_description', 'or_client_currency'], 'safe'],
            [['or_app_total', 'or_app_markup', 'or_agent_markup', 'or_client_total', 'or_client_currency_rate', 'or_profit_amount'], 'number'],

            ['or_created_dt', 'date', 'format' => 'php:Y-m-d'],
            ['or_updated_dt', 'date', 'format' => 'php:Y-m-d']
        ];
    }

    public function searchNew($params)
    {
        $query = self::find()->select('*')/*->with(['orLead', 'orOwnerUser', 'orCreatedUser', 'orUpdatedUser', 'productQuotes.pqProduct.prType'])*/;
        //$query->where();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'or_id' => SORT_DESC,
                ]
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        return $dataProvider;
    }
}
