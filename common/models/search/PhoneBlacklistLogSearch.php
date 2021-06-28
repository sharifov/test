<?php

namespace common\models\search;

use yii\data\ActiveDataProvider;
use common\models\PhoneBlacklistLog;

class PhoneBlacklistLogSearch extends PhoneBlacklistLog
{
    public function rules(): array
    {
        return [
            ['pbll_created_dt', 'safe'],

            ['pbll_created_user_id', 'integer'],

            ['pbll_id', 'integer'],

            ['pbll_phone', 'safe'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['pbll_id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'pbll_id' => $this->pbll_id,
            'DATE(pbll_created_dt)' => $this->pbll_created_dt,
            'pbll_created_user_id' => $this->pbll_created_user_id,
        ]);

        $query->andFilterWhere(['like', 'pbll_phone', $this->pbll_phone]);

        return $dataProvider;
    }
}
