<?php

namespace sales\model\clientChatChannelTransfer\entity\search;

use common\models\Employee;
use yii\data\ActiveDataProvider;
use sales\model\clientChatChannelTransfer\entity\ClientChatChannelTransfer;

class ClientChatChannelTransferSearch extends ClientChatChannelTransfer
{
    public function rules(): array
    {
        return [
            ['cctr_created_dt', 'date', 'format' => 'php:Y-m-d'],

            ['cctr_created_user_id', 'integer'],

            ['cctr_from_ccc_id', 'integer'],

            ['cctr_to_ccc_id', 'integer'],
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

        if ($this->cctr_created_dt) {
            \sales\helpers\query\QueryHelper::dayEqualByUserTZ($query, 'cctr_created_dt', $this->cctr_created_dt, $user->timezone);
        }

        $query->andFilterWhere([
            'cctr_from_ccc_id' => $this->cctr_from_ccc_id,
            'cctr_to_ccc_id' => $this->cctr_to_ccc_id,
            'cctr_created_user_id' => $this->cctr_created_user_id,
        ]);

        return $dataProvider;
    }
}
