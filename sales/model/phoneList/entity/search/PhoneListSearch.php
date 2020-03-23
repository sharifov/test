<?php

namespace sales\model\phoneList\entity\search;

use common\models\Employee;
use sales\helpers\query\QueryHelper;
use yii\data\ActiveDataProvider;
use sales\model\phoneList\entity\PhoneList;

class PhoneListSearch extends PhoneList
{
    public function rules():array
    {
        return [
            [['pl_id', 'pl_created_user_id', 'pl_updated_user_id'], 'integer'],

            ['pl_enabled', 'boolean'],

            ['pl_title', 'string'],

            ['pl_phone_number', 'safe'],

            [['pl_created_dt', 'pl_updated_dt'], 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public function search($params, Employee $user): ActiveDataProvider
    {
        $query = PhoneList::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->pl_created_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'pl_created_dt', $this->pl_created_dt, $user->timezone);
        }

        if ($this->pl_updated_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'pl_updated_dt', $this->pl_updated_dt, $user->timezone);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'pl_id' => $this->pl_id,
            'pl_enabled' => $this->pl_enabled,
            'pl_created_user_id' => $this->pl_created_user_id,
            'pl_updated_user_id' => $this->pl_updated_user_id,
        ]);

        $query->andFilterWhere(['like', 'pl_phone_number', $this->pl_phone_number])
            ->andFilterWhere(['like', 'pl_title', $this->pl_title]);

        return $dataProvider;
    }
}
