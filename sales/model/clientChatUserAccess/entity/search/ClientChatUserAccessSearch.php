<?php

namespace sales\model\clientChatUserAccess\entity\search;

use yii\data\ActiveDataProvider;
use sales\model\clientChatUserAccess\entity\ClientChatUserAccess;

class ClientChatUserAccessSearch extends ClientChatUserAccess
{
    public function rules(): array
    {
        return [
            ['ccua_id', 'integer'],

            ['ccua_cch_id', 'integer'],

            ['ccua_created_dt', 'safe'],

            ['ccua_status_id', 'integer'],

            ['ccua_updated_dt', 'safe'],

            ['ccua_user_id', 'integer'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['ccua_id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'ccua_id' => $this->ccua_id,
            'ccua_cch_id' => $this->ccua_cch_id,
            'ccua_user_id' => $this->ccua_user_id,
            'ccua_status_id' => $this->ccua_status_id,
            'date_format(ccua_created_dt, "%Y-%m-%d")' => $this->ccua_created_dt,
            'date_format(ccua_updated_dt, "%Y-%m-%d")' => $this->ccua_updated_dt,
        ]);

        return $dataProvider;
    }
}
