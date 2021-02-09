<?php

namespace modules\fileStorage\src\entity\fileClient\search;

use yii\data\ActiveDataProvider;
use modules\fileStorage\src\entity\fileClient\FileClient;

class FileClientSearch extends FileClient
{
    public function rules(): array
    {
        return [
            ['fcl_client_id', 'integer'],

            ['fcl_fs_id', 'integer'],
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
            'fcl_fs_id' => $this->fcl_fs_id,
            'fcl_client_id' => $this->fcl_client_id,
        ]);

        return $dataProvider;
    }
}
