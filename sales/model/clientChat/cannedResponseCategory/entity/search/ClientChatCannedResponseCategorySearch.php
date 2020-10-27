<?php

namespace sales\model\clientChat\cannedResponseCategory\entity\search;

use yii\data\ActiveDataProvider;
use sales\model\clientChat\cannedResponseCategory\entity\ClientChatCannedResponseCategory;

class ClientChatCannedResponseCategorySearch extends ClientChatCannedResponseCategory
{
    public function rules(): array
    {
        return [
            ['crc_created_dt', 'safe'],

            ['crc_created_user_id', 'integer'],

            ['crc_enabled', 'integer'],

            ['crc_id', 'integer'],

            ['crc_name', 'safe'],

            ['crc_updated_dt', 'safe'],

            ['crc_updated_user_id', 'integer'],
        ];
    }

    public function search($params): ActiveDataProvider
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

        $query->andFilterWhere([
            'crc_id' => $this->crc_id,
            'crc_enabled' => $this->crc_enabled,
            'date_format(crc_created_dt, "%Y-%m-%d")' => $this->crc_created_dt,
            'date_format(crc_updated_dt, "%Y-%m-%d")' => $this->crc_updated_dt,
            'crc_created_user_id' => $this->crc_created_user_id,
            'crc_updated_user_id' => $this->crc_updated_user_id,
        ]);

        $query->andFilterWhere(['like', 'crc_name', $this->crc_name]);

        return $dataProvider;
    }
}
