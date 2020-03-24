<?php

namespace sales\model\emailList\entity\search;

use common\models\Employee;
use sales\helpers\query\QueryHelper;
use sales\model\emailList\entity\EmailList;
use yii\data\ActiveDataProvider;

class EmailListSearch extends EmailList
{
    public function rules():array
    {
        return [
            [['el_id', 'el_created_user_id', 'el_updated_user_id'], 'integer'],

            ['el_enabled', 'boolean'],

            ['el_title', 'string'],

            ['el_email', 'string'],

            [['el_created_dt', 'el_updated_dt'], 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public function search($params, Employee $user): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->el_created_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'el_created_dt', $this->el_created_dt, $user->timezone);
        }

        if ($this->el_updated_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'el_updated_dt', $this->el_updated_dt, $user->timezone);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'el_id' => $this->el_id,
            'el_enabled' => $this->el_enabled,
            'el_created_user_id' => $this->el_created_user_id,
            'el_updated_user_id' => $this->el_updated_user_id,
        ]);

        $query->andFilterWhere(['like', 'el_email', $this->el_email])
            ->andFilterWhere(['like', 'el_title', $this->el_title]);

        return $dataProvider;
    }
}
