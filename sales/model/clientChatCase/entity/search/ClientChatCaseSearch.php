<?php

namespace sales\model\clientChatCase\entity\search;

use common\models\Employee;
use sales\model\clientChatCase\entity\ClientChatCase;
use yii\data\ActiveDataProvider;

class ClientChatCaseSearch extends ClientChatCase
{
    public function rules(): array
    {
        return [
            ['cccs_case_id', 'integer'],

            ['cccs_chat_id', 'integer'],

            ['cccs_created_dt', 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public function search($params, Employee $user): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['cccs_chat_id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->cccs_created_dt) {
            \sales\helpers\query\QueryHelper::dayEqualByUserTZ($query, 'cccs_created_dt', $this->cccs_created_dt, $user->timezone);
        }

        $query->andFilterWhere([
            'cccs_case_id' => $this->cccs_case_id,
            'cccs_chat_id' => $this->cccs_chat_id,
        ]);

        return $dataProvider;
    }
}
