<?php

namespace modules\order\src\entities\order\search;

use common\models\Employee;
use sales\helpers\query\QueryHelper;
use yii\data\ActiveDataProvider;
use modules\order\src\entities\order\Order;

class OrderCrudSearch extends Order
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

    public function search($params, Employee $user): ActiveDataProvider
    {
        $query = self::find()->with(['orLead', 'orOwnerUser', 'orCreatedUser', 'orUpdatedUser']);

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

        if ($this->or_created_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'or_created_dt', $this->or_created_dt, $user->timezone);
        }

        if ($this->or_updated_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'or_updated_dt', $this->or_updated_dt, $user->timezone);
        }

        // grid filtering conditions
        $query->andFilterWhere([
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
        ]);

        $query->andFilterWhere(['like', 'or_gid', $this->or_gid])
            ->andFilterWhere(['like', 'or_uid', $this->or_uid])
            ->andFilterWhere(['like', 'or_name', $this->or_name])
            ->andFilterWhere(['like', 'or_description', $this->or_description])
            ->andFilterWhere(['like', 'or_client_currency', $this->or_client_currency]);

        return $dataProvider;
    }
}
