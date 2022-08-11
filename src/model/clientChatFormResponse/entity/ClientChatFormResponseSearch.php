<?php

namespace src\model\clientChatFormResponse\entity;

use common\models\Employee;
use yii\data\ActiveDataProvider;

/**
 * ClientChatFormResponseSearch represents the model behind the search form of `src\model\clientChatFormResponse\entity\ClientChatFormResponse`.
 */
class ClientChatFormResponseSearch extends ClientChatFormResponse
{
    public function rules(): array
    {
        return [
            [['ccfr_id', 'ccfr_client_chat_id', 'ccfr_form_id'], 'integer'],
            [['ccfr_value', 'ccfr_uid'], 'string'],
            [['ccfr_created_dt', 'ccfr_rc_created_dt'], 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = ClientChatFormResponse::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['ccfr_id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'ccfr_id' => $this->ccfr_id,
            'ccfr_client_chat_id' => $this->ccfr_client_chat_id,
            'ccfr_form_id' => $this->ccfr_form_id,
            'DATE(ccfr_rc_created_dt)' => $this->ccfr_rc_created_dt,
            'DATE(ccfr_created_dt)' => $this->ccfr_created_dt,
        ]);

        $query->andFilterWhere(['like', 'ccfr_value', $this->ccfr_value]);
        $query->andFilterWhere(['like', 'ccfr_uid', $this->ccfr_uid]);

        return $dataProvider;
    }
}
