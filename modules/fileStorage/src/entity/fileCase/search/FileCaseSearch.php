<?php

namespace modules\fileStorage\src\entity\fileCase\search;

use yii\data\ActiveDataProvider;
use modules\fileStorage\src\entity\fileCase\FileCase;

class FileCaseSearch extends FileCase
{
    public function rules(): array
    {
        return [
            ['fc_case_id', 'integer'],

            ['fc_fs_id', 'integer'],
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
            'fc_fs_id' => $this->fc_fs_id,
            'fc_case_id' => $this->fc_case_id,
        ]);

        return $dataProvider;
    }
}
