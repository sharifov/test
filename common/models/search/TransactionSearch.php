<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Transaction;

/**
 * TransactionSearch represents the model behind the search form of `common\models\Transaction`.
 */
class TransactionSearch extends Transaction
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tr_id', 'tr_invoice_id', 'tr_payment_id', 'tr_type_id'], 'integer'],
            [['tr_code', 'tr_date', 'tr_currency', 'tr_created_dt'], 'safe'],
            [['tr_amount'], 'number'],
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
        $query = Transaction::find();

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
            'tr_id' => $this->tr_id,
            'tr_invoice_id' => $this->tr_invoice_id,
            'tr_payment_id' => $this->tr_payment_id,
            'tr_type_id' => $this->tr_type_id,
            'tr_date' => $this->tr_date,
            'tr_amount' => $this->tr_amount,
            'tr_created_dt' => $this->tr_created_dt,
        ]);

        $query->andFilterWhere(['like', 'tr_code', $this->tr_code])
            ->andFilterWhere(['like', 'tr_currency', $this->tr_currency]);

        return $dataProvider;
    }
}
