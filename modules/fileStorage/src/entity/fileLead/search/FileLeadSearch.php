<?php

namespace modules\fileStorage\src\entity\fileLead\search;

use yii\data\ActiveDataProvider;
use modules\fileStorage\src\entity\fileLead\FileLead;

class FileLeadSearch extends FileLead
{
    public function rules(): array
    {
        return [
            ['fld_fs_id', 'integer'],

            ['fld_lead_id', 'integer'],
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
            'fld_fs_id' => $this->fld_fs_id,
            'fld_lead_id' => $this->fld_lead_id,
        ]);

        return $dataProvider;
    }
}
