<?php

namespace modules\fileStorage\src\entity\fileUser\search;

use yii\data\ActiveDataProvider;
use modules\fileStorage\src\entity\fileUser\FileUser;

class FileUserSearch extends FileUser
{
    public function rules(): array
    {
        return [
            ['fus_fs_id', 'integer'],

            ['fus_user_id', 'integer'],
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
            'fus_fs_id' => $this->fus_fs_id,
            'fus_user_id' => $this->fus_user_id,
        ]);

        return $dataProvider;
    }
}
