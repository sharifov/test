<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Order;

/**
 * OrderSearch represents the model behind the search form of `common\models\Order`.
 */
class OrderSearch extends Order
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['or_id', 'or_lead_id', 'or_status_id', 'or_pay_status_id', 'or_owner_user_id', 'or_created_user_id', 'or_updated_user_id'], 'integer'],
            [['or_gid', 'or_uid', 'or_name', 'or_description', 'or_client_currency', 'or_created_dt', 'or_updated_dt'], 'safe'],
            [['or_app_total', 'or_app_markup', 'or_agent_markup', 'or_client_total', 'or_client_currency_rate'], 'number'],
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
        $query = Order::find();

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
            'or_created_dt' => $this->or_created_dt,
            'or_updated_dt' => $this->or_updated_dt,
        ]);

        $query->andFilterWhere(['like', 'or_gid', $this->or_gid])
            ->andFilterWhere(['like', 'or_uid', $this->or_uid])
            ->andFilterWhere(['like', 'or_name', $this->or_name])
            ->andFilterWhere(['like', 'or_description', $this->or_description])
            ->andFilterWhere(['like', 'or_client_currency', $this->or_client_currency]);

        return $dataProvider;
    }


    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchByLead($params)
    {
        $query = Order::find();

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
            'or_created_dt' => $this->or_created_dt,
            'or_updated_dt' => $this->or_updated_dt,
        ]);

        return $dataProvider;
    }
}
