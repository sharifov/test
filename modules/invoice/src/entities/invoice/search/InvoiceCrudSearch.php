<?php

namespace modules\invoice\src\entities\invoice\search;

use common\models\Employee;
use sales\helpers\query\QueryHelper;
use yii\data\ActiveDataProvider;
use modules\invoice\src\entities\invoice\Invoice;

class InvoiceCrudSearch extends Invoice
{
    public function rules(): array
    {
        return [
            [['inv_id', 'inv_order_id', 'inv_status_id', 'inv_created_user_id', 'inv_updated_user_id'], 'integer'],
            [['inv_gid', 'inv_uid', 'inv_client_currency', 'inv_description'], 'safe'],
            [['inv_sum', 'inv_client_sum', 'inv_currency_rate'], 'number'],

            ['inv_created_dt', 'date', 'format' => 'php:Y-m-d'],
            ['inv_updated_dt', 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public function search($params, Employee $user): ActiveDataProvider
    {
        $query = Invoice::find()->with(['invOrder', 'invCreatedUser', 'invUpdatedUser']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->inv_created_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'inv_created_dt', $this->inv_created_dt, $user->timezone);
        }

        if ($this->inv_updated_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'inv_updated_dt', $this->inv_updated_dt, $user->timezone);
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
        ]);

        $query->andFilterWhere(['like', 'inv_gid', $this->inv_gid])
            ->andFilterWhere(['like', 'inv_uid', $this->inv_uid])
            ->andFilterWhere(['like', 'inv_client_currency', $this->inv_client_currency])
            ->andFilterWhere(['like', 'inv_description', $this->inv_description]);

        return $dataProvider;
    }
}
