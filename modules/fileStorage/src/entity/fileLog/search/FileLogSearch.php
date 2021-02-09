<?php

namespace modules\fileStorage\src\entity\fileLog\search;

use yii\data\ActiveDataProvider;
use modules\fileStorage\src\entity\fileLog\FileLog;

class FileLogSearch extends FileLog
{
    public function rules(): array
    {
        return [
            ['fl_fs_id', 'integer'],

            ['fl_fsh_id', 'integer'],

            ['fl_id', 'integer'],

            ['fl_type_id', 'integer'],
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
            'fl_id' => $this->fl_id,
            'fl_fs_id' => $this->fl_fs_id,
            'fl_fsh_id' => $this->fl_fsh_id,
            'fl_type_id' => $this->fl_type_id,
        ]);

        return $dataProvider;
    }
}
