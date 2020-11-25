<?php

namespace sales\model\clientChatLastMessage\entity;

use common\models\Employee;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use sales\model\clientChatLastMessage\entity\ClientChatLastMessage;

/**
 * ClientChatLastMessageSearch
 */
class ClientChatLastMessageSearch extends ClientChatLastMessage
{
    public function rules(): array
    {
        return [
            [['cclm_id', 'cclm_cch_id', 'cclm_type_id'], 'integer'],
            [['cclm_message'], 'safe'],
            [['cclm_dt'], 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    /**
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params): ActiveDataProvider
    {
        $query = ClientChatLastMessage::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['cclm_dt' => SORT_DESC]],
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
            'cclm_id' => $this->cclm_id,
            'cclm_cch_id' => $this->cclm_cch_id,
            'cclm_type_id' => $this->cclm_type_id,
        ]);

        $query->andFilterWhere(['like', 'cclm_message', $this->cclm_message]);

        if ($this->cclm_dt) {
            $query->andFilterWhere(['>=', 'cclm_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->cclm_dt))])
                ->andFilterWhere(['<=', 'cclm_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->cclm_dt) + 3600 * 24)]);
        }

        return $dataProvider;
    }
}
