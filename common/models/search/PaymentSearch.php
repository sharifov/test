<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Payment;

/**
 * PaymentSearch represents the model behind the search form of `common\models\Payment`.
 */
class PaymentSearch extends Payment
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'pay_id', 'pay_type_id', 'pay_method_id', 'pay_status_id', 'pay_invoice_id',
                'pay_order_id', 'pay_created_user_id', 'pay_updated_user_id', 'pay_billing_id'
            ], 'integer'],
            [['pay_currency'], 'safe'],
            [['pay_amount'], 'number'],
            [['pay_code'], 'string'],
            [['pay_created_dt', 'pay_updated_dt', 'pay_date'], 'date', 'format' => 'php:Y-m-d'],

            [['pay_description'], 'string', 'max' => 255],
        ];
    }

    /**
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Payment::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['pay_id' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'pay_id' => $this->pay_id,
            'pay_type_id' => $this->pay_type_id,
            'pay_method_id' => $this->pay_method_id,
            'pay_status_id' => $this->pay_status_id,
            'pay_code' => $this->pay_code,
            'DATE(pay_date)' => $this->pay_date,
            'pay_amount' => $this->pay_amount,
            'pay_invoice_id' => $this->pay_invoice_id,
            'pay_order_id' => $this->pay_order_id,
            'pay_created_user_id' => $this->pay_created_user_id,
            'pay_updated_user_id' => $this->pay_updated_user_id,
            'DATE(pay_created_dt)' => $this->pay_created_dt,
            'DATE(pay_updated_dt)' => $this->pay_updated_dt,
            'pay_billing_id' => $this->pay_billing_id,
        ]);

        $query->andFilterWhere(['like', 'pay_currency', $this->pay_currency]);
        $query->andFilterWhere(['like', 'pay_description', $this->pay_description]);

        return $dataProvider;
    }
}
