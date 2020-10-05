<?php

namespace sales\model\clientChatUnread\entity\search;

use common\models\Employee;
use yii\data\ActiveDataProvider;
use sales\model\clientChatUnread\entity\ClientChatUnread;

class ClientChatUnreadSearch extends ClientChatUnread
{
    public function rules(): array
    {
        return [
            ['ccu_created_dt', 'date', 'format' => 'php:Y-m-d'],

            ['ccu_updated_dt', 'date', 'format' => 'php:Y-m-d'],

            ['ccu_cc_id', 'integer'],

            ['ccu_count', 'integer'],
        ];
    }

    public function search($params, Employee $user): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->ccu_created_dt) {
            \sales\helpers\query\QueryHelper::dayEqualByUserTZ($query, 'ccu_created_dt', $this->ccu_created_dt, $user->timezone);
        }
        if ($this->ccu_updated_dt) {
            \sales\helpers\query\QueryHelper::dayEqualByUserTZ($query, 'ccu_updated_dt', $this->ccu_updated_dt, $user->timezone);
        }

        $query->andFilterWhere([
            'ccu_cc_id' => $this->ccu_cc_id,
            'ccu_count' => $this->ccu_count,
        ]);

        return $dataProvider;
    }
}
