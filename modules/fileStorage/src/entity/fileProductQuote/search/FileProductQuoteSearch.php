<?php

namespace modules\fileStorage\src\entity\fileProductQuote\search;

use modules\fileStorage\src\entity\fileProductQuote\FileProductQuote;
use yii\data\ActiveDataProvider;

class FileProductQuoteSearch extends FileProductQuote
{
    public function rules(): array
    {
        return [
            ['fpq_fs_id', 'integer'],
            ['fpq_pq_id', 'integer'],

            [['fpq_created_dt'], 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['fpq_created_dt' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'fld_fs_id' => $this->fpq_fs_id,
            'fld_lead_id' => $this->fpq_pq_id,
            'DATE(fpq_created_dt)' => $this->fpq_created_dt,
        ]);

        return $dataProvider;
    }
}
