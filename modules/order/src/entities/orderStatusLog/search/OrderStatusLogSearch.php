<?php

namespace modules\order\src\entities\orderStatusLog\search;

use common\models\Employee;
use modules\order\src\entities\order\OrderStatus;
use modules\order\src\entities\order\OrderStatusAction;
use modules\order\src\entities\orderStatusLog\OrderStatusLog;
use sales\helpers\query\QueryHelper;
use yii\data\ActiveDataProvider;

class OrderStatusLogSearch extends OrderStatusLog
{
    public function rules(): array
    {
        return [
            ['orsl_id', 'integer'],
            ['orsl_id', 'exist', 'skipOnError' => true, 'targetClass' => static::class, 'targetAttribute' => ['orsl_id' => 'orsl_id']],

            ['orsl_start_status_id', 'integer'],
            ['orsl_start_status_id', 'in', 'range' => array_keys(OrderStatus::getList())],

            ['orsl_end_status_id', 'integer'],
            ['orsl_end_status_id', 'in', 'range' => array_keys(OrderStatus::getList())],

            ['orsl_start_dt', 'date', 'format' => 'php:Y-m-d'],

            ['orsl_end_dt', 'date', 'format' => 'php:Y-m-d'],

            ['orsl_duration', 'integer'],

            ['orsl_description', 'string', 'max' => 255],

            ['orsl_action_id', 'integer'],
            ['orsl_action_id', 'in', 'range' => array_keys(OrderStatusAction::getList())],

            ['orsl_owner_user_id', 'integer'],
            ['orsl_owner_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['orsl_owner_user_id' => 'id']],

            ['orsl_created_user_id', 'integer'],
            ['orsl_created_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['orsl_created_user_id' => 'id']],
        ];
    }

    public function search($params, Employee $user, int $orderId): ActiveDataProvider
    {
        $query = self::find()->with(['createdUser', 'ownerUser', 'order']);

        $query->andWhere(['orsl_order_id' => $orderId]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->orsl_start_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'orsl_start_dt', $this->orsl_start_dt, $user->timezone);
        }

        if ($this->orsl_end_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'orsl_end_dt', $this->orsl_end_dt, $user->timezone);
        }

        $query->andFilterWhere([
            'orsl_id' => $this->orsl_id,
            'orsl_start_status_id' => $this->orsl_start_status_id,
            'orsl_end_status_id' => $this->orsl_end_status_id,
            'orsl_duration' => $this->orsl_duration,
            'orsl_action_id' => $this->orsl_action_id,
            'orsl_owner_user_id' => $this->orsl_owner_user_id,
            'orsl_created_user_id' => $this->orsl_created_user_id,
        ]);

        $query->andFilterWhere(['like', 'orsl_description', $this->orsl_description]);

        return $dataProvider;
    }
}
