<?php

namespace sales\model\shiftSchedule\entity\userShiftAssign\search;

use yii\data\ActiveDataProvider;
use sales\model\shiftSchedule\entity\userShiftAssign\UserShiftAssign;

class SearchUserShiftAssign extends UserShiftAssign
{
    public function rules(): array
    {
        return [
            ['usa_created_dt', 'safe'],

            ['usa_created_user_id', 'integer'],

            ['usa_ssr_id', 'integer'],

            ['usa_user_id', 'integer'],
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
            'usa_user_id' => $this->usa_user_id,
            'usa_ssr_id' => $this->usa_ssr_id,
            'date(usa_created_dt)' => $this->usa_created_dt,
            'usa_created_user_id' => $this->usa_created_user_id,
        ]);

        return $dataProvider;
    }
}
