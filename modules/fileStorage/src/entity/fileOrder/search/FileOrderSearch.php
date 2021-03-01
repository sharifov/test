<?php

namespace modules\fileStorage\src\entity\fileOrder\search;

use yii\data\ActiveDataProvider;
use modules\fileStorage\src\entity\fileOrder\FileOrder;

class FileOrderSearch extends FileOrder
{
    public function rules(): array
    {
        return [
            [['fo_id', 'fo_fs_id', 'fo_or_id', 'fo_pq_id', 'fo_category_id'], 'integer'],
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
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'fo_id' => $this->fo_id,
            'fo_fs_id' => $this->fo_fs_id,
            'fo_or_id' => $this->fo_or_id,
            'fo_pq_id' => $this->fo_pq_id,
            'fo_category_id' => $this->fo_category_id,
        ]);

        return $dataProvider;
    }
}
