<?php

namespace modules\order\src\entities\order\search;

use common\models\Employee;
use modules\order\src\entities\order\Order;
use modules\order\src\entities\order\OrderStatus;
use sales\helpers\query\QueryHelper;
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

    public function searchNew($params, Employee $user): ActiveDataProvider
    {
        $query = self::find()->select('*');

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

        if ($this->or_created_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'or_created_dt', $this->or_created_dt, $user->timezone);
        }

        $query->andFilterWhere([
            'or_id' => $this->or_id,
            'or_project_id' => $this->or_project_id,
            'or_app_total' => $this->or_app_total,
            'or_owner_user_id' => $this->or_owner_user_id,
            'or_type_id' => $this->or_type_id,
            'or_status_id' => OrderStatus::NEW,
            'or_pay_status_id' => $this->or_pay_status_id,
        ]);

        $query->andFilterWhere(['like', 'or_fare_id', $this->or_fare_id])
            ->andFilterWhere(['like', 'or_uid', $this->or_uid]);

        return $dataProvider;
    }

    public function searchPending($params, Employee $user): ActiveDataProvider
    {
        $query = self::find()->select('*')->with(['orOwnerUser']);

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

        if ($this->or_created_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'or_created_dt', $this->or_created_dt, $user->timezone);
        }

        $query->andFilterWhere([
            'or_id' => $this->or_id,
            'or_project_id' => $this->or_project_id,
            'or_app_total' => $this->or_app_total,
            'or_owner_user_id' => $this->or_owner_user_id,
            'or_type_id' => $this->or_type_id,
            'or_status_id' => OrderStatus::PENDING,
            'or_pay_status_id' => $this->or_pay_status_id,
        ]);

        $query->andFilterWhere(['like', 'or_fare_id', $this->or_fare_id])
            ->andFilterWhere(['like', 'or_uid', $this->or_uid]);

        return $dataProvider;
    }

    public function searchProcessing($params, Employee $user): ActiveDataProvider
    {
        $query = self::find()->select('*')->with(['orOwnerUser']);

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

        if ($this->or_created_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'or_created_dt', $this->or_created_dt, $user->timezone);
        }

        $query->andFilterWhere([
            'or_id' => $this->or_id,
            'or_project_id' => $this->or_project_id,
            'or_app_total' => $this->or_app_total,
            'or_owner_user_id' => $this->or_owner_user_id,
            'or_type_id' => $this->or_type_id,
            'or_status_id' => OrderStatus::PROCESSING,
            'or_pay_status_id' => $this->or_pay_status_id,
        ]);

        $query->andFilterWhere(['like', 'or_fare_id', $this->or_fare_id])
            ->andFilterWhere(['like', 'or_uid', $this->or_uid]);

        return $dataProvider;
    }

    public function searchPrepared($params, Employee $user): ActiveDataProvider
    {
        $query = self::find()->select('*')->with(['orOwnerUser']);

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

        if ($this->or_created_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'or_created_dt', $this->or_created_dt, $user->timezone);
        }

        $query->andFilterWhere([
            'or_id' => $this->or_id,
            'or_project_id' => $this->or_project_id,
            'or_app_total' => $this->or_app_total,
            'or_owner_user_id' => $this->or_owner_user_id,
            'or_type_id' => $this->or_type_id,
            'or_status_id' => OrderStatus::PREPARED,
            'or_pay_status_id' => $this->or_pay_status_id,
        ]);

        $query->andFilterWhere(['like', 'or_fare_id', $this->or_fare_id])
            ->andFilterWhere(['like', 'or_uid', $this->or_uid]);

        return $dataProvider;
    }

    public function searchComplete($params, Employee $user): ActiveDataProvider
    {
        $query = self::find()->select('*')->with(['orOwnerUser']);

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

        if ($this->or_created_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'or_created_dt', $this->or_created_dt, $user->timezone);
        }

        $query->andFilterWhere([
            'or_id' => $this->or_id,
            'or_project_id' => $this->or_project_id,
            'or_app_total' => $this->or_app_total,
            'or_owner_user_id' => $this->or_owner_user_id,
            'or_type_id' => $this->or_type_id,
            'or_status_id' => OrderStatus::COMPLETE,
            'or_pay_status_id' => $this->or_pay_status_id,
        ]);

        $query->andFilterWhere(['like', 'or_fare_id', $this->or_fare_id])
            ->andFilterWhere(['like', 'or_uid', $this->or_uid]);

        return $dataProvider;
    }

    public function searchCancelProcessing($params, Employee $user): ActiveDataProvider
    {
        $query = self::find()->select('*')->with(['orOwnerUser']);

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

        if ($this->or_created_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'or_created_dt', $this->or_created_dt, $user->timezone);
        }

        $query->andFilterWhere([
            'or_id' => $this->or_id,
            'or_project_id' => $this->or_project_id,
            'or_app_total' => $this->or_app_total,
            'or_owner_user_id' => $this->or_owner_user_id,
            'or_type_id' => $this->or_type_id,
            'or_status_id' => OrderStatus::CANCEL_PROCESSING,
            'or_pay_status_id' => $this->or_pay_status_id,
        ]);

        $query->andFilterWhere(['like', 'or_fare_id', $this->or_fare_id])
            ->andFilterWhere(['like', 'or_uid', $this->or_uid]);

        return $dataProvider;
    }

    public function searchError($params, Employee $user): ActiveDataProvider
    {
        $query = self::find()->select('*')->with(['orOwnerUser']);

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

        if ($this->or_created_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'or_created_dt', $this->or_created_dt, $user->timezone);
        }

        $query->andFilterWhere([
            'or_id' => $this->or_id,
            'or_project_id' => $this->or_project_id,
            'or_app_total' => $this->or_app_total,
            'or_owner_user_id' => $this->or_owner_user_id,
            'or_type_id' => $this->or_type_id,
            'or_status_id' => OrderStatus::ERROR,
            'or_pay_status_id' => $this->or_pay_status_id,
        ]);

        $query->andFilterWhere(['like', 'or_fare_id', $this->or_fare_id])
            ->andFilterWhere(['like', 'or_uid', $this->or_uid]);

        return $dataProvider;
    }

    public function searchDeclined($params, Employee $user): ActiveDataProvider
    {
        $query = self::find()->select('*')->with(['orOwnerUser']);

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

        if ($this->or_created_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'or_created_dt', $this->or_created_dt, $user->timezone);
        }

        $query->andFilterWhere([
            'or_id' => $this->or_id,
            'or_project_id' => $this->or_project_id,
            'or_app_total' => $this->or_app_total,
            'or_owner_user_id' => $this->or_owner_user_id,
            'or_type_id' => $this->or_type_id,
            'or_status_id' => OrderStatus::DECLINED,
            'or_pay_status_id' => $this->or_pay_status_id,
        ]);

        $query->andFilterWhere(['like', 'or_fare_id', $this->or_fare_id])
            ->andFilterWhere(['like', 'or_uid', $this->or_uid]);

        return $dataProvider;
    }

    public function searchCanceled($params, Employee $user): ActiveDataProvider
    {
        $query = self::find()->select('*')->with(['orOwnerUser']);

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

        if ($this->or_created_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'or_created_dt', $this->or_created_dt, $user->timezone);
        }

        $query->andFilterWhere([
            'or_id' => $this->or_id,
            'or_project_id' => $this->or_project_id,
            'or_app_total' => $this->or_app_total,
            'or_owner_user_id' => $this->or_owner_user_id,
            'or_type_id' => $this->or_type_id,
            'or_status_id' => OrderStatus::CANCELED,
            'or_pay_status_id' => $this->or_pay_status_id,
        ]);

        $query->andFilterWhere(['like', 'or_fare_id', $this->or_fare_id])
            ->andFilterWhere(['like', 'or_uid', $this->or_uid]);

        return $dataProvider;
    }

    public function searchCancelFailed($params, Employee $user): ActiveDataProvider
    {
        $query = self::find()->select('*')->with(['orOwnerUser']);

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

        if ($this->or_created_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'or_created_dt', $this->or_created_dt, $user->timezone);
        }

        $query->andFilterWhere([
            'or_id' => $this->or_id,
            'or_project_id' => $this->or_project_id,
            'or_app_total' => $this->or_app_total,
            'or_owner_user_id' => $this->or_owner_user_id,
            'or_type_id' => $this->or_type_id,
            'or_status_id' => OrderStatus::CANCEL_FAILED,
            'or_pay_status_id' => $this->or_pay_status_id,
        ]);

        $query->andFilterWhere(['like', 'or_fare_id', $this->or_fare_id])
            ->andFilterWhere(['like', 'or_uid', $this->or_uid]);

        return $dataProvider;
    }
}
