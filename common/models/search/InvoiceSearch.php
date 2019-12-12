<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Invoice;

/**
 * InvoiceSearch represents the model behind the search form of `common\models\Invoice`.
 */
class InvoiceSearch extends Invoice
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['inv_id', 'inv_order_id', 'inv_status_id', 'inv_created_user_id', 'inv_updated_user_id'], 'integer'],
            [['inv_gid', 'inv_uid', 'inv_client_currency', 'inv_description', 'inv_created_dt', 'inv_updated_dt'], 'safe'],
            [['inv_sum', 'inv_client_sum', 'inv_currency_rate'], 'number'],
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
        $query = Invoice::find();

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
            'inv_id' => $this->inv_id,
            'inv_order_id' => $this->inv_order_id,
            'inv_status_id' => $this->inv_status_id,
            'inv_sum' => $this->inv_sum,
            'inv_client_sum' => $this->inv_client_sum,
            'inv_currency_rate' => $this->inv_currency_rate,
            'inv_created_user_id' => $this->inv_created_user_id,
            'inv_updated_user_id' => $this->inv_updated_user_id,
            'inv_created_dt' => $this->inv_created_dt,
            'inv_updated_dt' => $this->inv_updated_dt,
        ]);

        $query->andFilterWhere(['like', 'inv_gid', $this->inv_gid])
            ->andFilterWhere(['like', 'inv_uid', $this->inv_uid])
            ->andFilterWhere(['like', 'inv_client_currency', $this->inv_client_currency])
            ->andFilterWhere(['like', 'inv_description', $this->inv_description]);

        return $dataProvider;
    }
}
