<?php

namespace modules\fileStorage\src\entity\fileStorage\search;

use yii\data\ActiveDataProvider;
use modules\fileStorage\src\entity\fileStorage\FileStorage;

class FileStorageSearch extends FileStorage
{
    public function rules(): array
    {
        return [
            ['fs_id', 'integer'],

            ['fs_mime_type', 'string'],

            ['fs_name', 'string'],

            ['fs_title', 'safe'],

            ['fs_uid', 'safe'],

            ['fs_status', 'integer']
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['fs_id' => SORT_DESC]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'fs_id' => $this->fs_id,
            'fs_mime_type' => $this->fs_mime_type,
            'fs_status' => $this->fs_status,
        ]);

        $query->andFilterWhere(['like', 'fs_uid', $this->fs_uid])
            ->andFilterWhere(['like', 'fs_name', $this->fs_name])
            ->andFilterWhere(['like', 'fs_title', $this->fs_title]);

        return $dataProvider;
    }
}
