<?php

namespace sales\model\userData\entity\search;

use common\models\Employee;
use yii\data\ActiveDataProvider;
use sales\model\userData\entity\UserData;

class UserDataSearch extends UserData
{
    public function rules(): array
    {
        return [
            ['ud_key', 'integer'],

            ['ud_updated_dt', 'date', 'format' => 'php:Y-m-d'],

            ['ud_user_id', 'integer'],

            ['ud_value', 'number'],
        ];
    }

    public function search($params, Employee $user): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'ud_updated_dt' => SORT_DESC,
                ]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->ud_updated_dt) {
            \sales\helpers\query\QueryHelper::dayEqualByUserTZ($query, 'ud_updated_dt', $this->ud_updated_dt, $user->timezone);
        }

        $query->andFilterWhere([
            'ud_user_id' => $this->ud_user_id,
            'ud_key' => $this->ud_key,
            'ud_value' => $this->ud_value,
        ]);

        return $dataProvider;
    }
}
