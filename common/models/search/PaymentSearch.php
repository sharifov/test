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
            [['pay_id', 'pay_type_id', 'pay_method_id', 'pay_status_id', 'pay_invoice_id', 'pay_order_id', 'pay_created_user_id', 'pay_updated_user_id'], 'integer'],
            [['pay_date', 'pay_currency', 'pay_created_dt', 'pay_updated_dt'], 'safe'],
            [['pay_amount'], 'number'],
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
        $query = Payment::find();

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
            'pay_id' => $this->pay_id,
            'pay_type_id' => $this->pay_type_id,
            'pay_method_id' => $this->pay_method_id,
            'pay_status_id' => $this->pay_status_id,
            'pay_date' => $this->pay_date,
            'pay_amount' => $this->pay_amount,
            'pay_invoice_id' => $this->pay_invoice_id,
            'pay_order_id' => $this->pay_order_id,
            'pay_created_user_id' => $this->pay_created_user_id,
            'pay_updated_user_id' => $this->pay_updated_user_id,
            'pay_created_dt' => $this->pay_created_dt,
            'pay_updated_dt' => $this->pay_updated_dt,
        ]);

        $query->andFilterWhere(['like', 'pay_currency', $this->pay_currency]);

        return $dataProvider;
    }
}
