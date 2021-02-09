<?php

namespace sales\model\shiftSchedule\entity\shift\search;

use yii\data\ActiveDataProvider;
use sales\model\shiftSchedule\entity\shift\Shift;

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
        ]);

        $query->andFilterWhere(['like', 'sh_name', $this->sh_name])
            ->andFilterWhere(['like', 'sh_color', $this->sh_color]);

        return $dataProvider;
    }
}
