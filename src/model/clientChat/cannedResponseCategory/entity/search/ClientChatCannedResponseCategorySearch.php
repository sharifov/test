<?php

namespace src\model\clientChat\cannedResponseCategory\entity\search;

use yii\data\ActiveDataProvider;
use src\model\clientChat\cannedResponseCategory\entity\ClientChatCannedResponseCategory;

class ClientChatCannedResponseCategorySearch extends ClientChatCannedResponseCategory
{
    public function rules(): array
    {
        return [

            [['crc_created_dt', 'crc_updated_dt'], 'date', 'format' => 'php:Y-m-d'],

            ['crc_created_user_id', 'integer'],

            ['crc_enabled', 'integer'],

            ['crc_id', 'integer'],

            ['crc_name', 'safe'],

            ['', 'safe'],

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
            'DATE(crc_created_dt)' => $this->crc_created_dt,
            'DATE(crc_updated_dt)' => $this->crc_updated_dt,
            'crc_created_user_id' => $this->crc_created_user_id,
            'crc_updated_user_id' => $this->crc_updated_user_id,
        ]);

        $query->andFilterWhere(['like', 'crc_name', $this->crc_name]);

        return $dataProvider;
    }
}
