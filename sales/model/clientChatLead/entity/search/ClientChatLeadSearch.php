<?php

namespace sales\model\clientChatLead\entity\search;

use common\models\Employee;
use sales\model\clientChatLead\entity\ClientChatLead;
use yii\data\ActiveDataProvider;

class ClientChatLeadSearch extends ClientChatLead
{
    public function rules(): array
    {
        return [
            ['ccl_lead_id', 'integer'],

            ['ccl_chat_id', 'integer'],

            ['ccl_created_dt', 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public function search($params, Employee $user): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['ccl_chat_id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->ccl_created_dt) {
            \sales\helpers\query\QueryHelper::dayEqualByUserTZ($query, 'ccl_created_dt', $this->ccl_created_dt, $user->timezone);
        }

        $query->andFilterWhere([
            'ccl_lead_id' => $this->ccl_lead_id,
            'ccl_chat_id' => $this->ccl_chat_id,
        ]);

        return $dataProvider;
    }
}
