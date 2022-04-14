<?php

namespace modules\shiftSchedule\src\entities\shift\search;

use modules\shiftSchedule\src\entities\shift\Shift;
use yii\data\ActiveDataProvider;

class SearchShift extends Shift
{
    public function rules(): array
    {
        return [
            ['sh_color', 'safe'],

            ['sh_created_dt', 'safe'],

            ['sh_created_user_id', 'integer'],

            ['sh_enabled', 'integer'],

            ['sh_id', 'integer'],

            ['sh_name', 'safe'],

            ['sh_sort_order', 'integer'],

            ['sh_updated_dt', 'safe'],

            ['sh_updated_user_id', 'integer'],
            ['sh_category_id', 'integer'],
            ['sh_title', 'string'],
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
            'sh_id' => $this->sh_id,
            'sh_enabled' => $this->sh_enabled,
            'sh_sort_order' => $this->sh_sort_order,
            'date(sh_created_dt)' => $this->sh_created_dt,
            'date(sh_updated_dt)' => $this->sh_updated_dt,
            'sh_created_user_id' => $this->sh_created_user_id,
            'sh_updated_user_id' => $this->sh_updated_user_id,
            'sh_category_id' => $this->sh_category_id,
        ]);

        $query->andFilterWhere(['like', 'sh_name', $this->sh_name])
            ->andFilterWhere(['like', 'sh_title', $this->sh_title])
            ->andFilterWhere(['like', 'sh_color', $this->sh_color]);

        return $dataProvider;
    }
}
