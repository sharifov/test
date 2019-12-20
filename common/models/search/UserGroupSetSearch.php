<?php

namespace common\models\search;

use common\models\Employee;
use sales\helpers\query\QueryHelper;
use yii\data\ActiveDataProvider;
use common\models\UserGroupSet;

/**
 * UserGroupSetSearch represents the model behind the search form of `common\models\UserGroupSet`.
 */
class UserGroupSetSearch extends UserGroupSet
{

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['ugs_id', 'ugs_updated_user_id'], 'integer'],

            ['ugs_enabled', 'boolean'],

            ['ugs_name', 'string'],

            [['ugs_created_dt', 'ugs_updated_dt'], 'datetime', 'format' => 'php:Y-m-d'],
        ];
    }

    /**
     * @param $params
     * @param Employee $user
     * @return ActiveDataProvider
     */
    public function search($params, $user): ActiveDataProvider
    {
        $query = UserGroupSet::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'ugs_id' => $this->ugs_id,
            'ugs_enabled' => $this->ugs_enabled,
            'ugs_updated_user_id' => $this->ugs_updated_user_id,
        ]);

        if ($this->ugs_created_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'ugs_created_dt', $this->ugs_created_dt, $user->timezone);
        }

        if ($this->ugs_updated_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'ugs_updated_dt', $this->ugs_updated_dt, $user->timezone);
        }

        $query->andFilterWhere(['like', 'ugs_name', $this->ugs_name]);

        return $dataProvider;
    }
}
