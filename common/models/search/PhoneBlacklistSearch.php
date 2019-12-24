<?php

namespace common\models\search;

use common\models\Employee;
use sales\helpers\query\QueryHelper;
use yii\data\ActiveDataProvider;
use common\models\PhoneBlacklist;

/**
 * PhoneBlacklistSearch represents the model behind the search form of `common\models\PhoneBlacklist`.
 */
class PhoneBlacklistSearch extends PhoneBlacklist
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['pbl_id', 'pbl_updated_user_id'], 'integer'],

            ['pbl_enabled', 'boolean'],

            [['pbl_phone', 'pbl_description'], 'string'],

            [['pbl_created_dt', 'pbl_updated_dt'], 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    /**
     * @param $params
     * @param Employee $user
     * @return ActiveDataProvider
     */
    public function search($params, Employee $user): ActiveDataProvider
    {
        $query = PhoneBlacklist::find()->with('updatedUser');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->pbl_created_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'pbl_created_dt', $this->pbl_created_dt, $user->timezone);
        }

        if ($this->pbl_updated_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'pbl_updated_dt', $this->pbl_updated_dt, $user->timezone);
        }

        $query->andFilterWhere([
            'pbl_id' => $this->pbl_id,
            'pbl_enabled' => $this->pbl_enabled,
            'pbl_updated_user_id' => $this->pbl_updated_user_id,
        ]);

        $query->andFilterWhere(['like', 'pbl_phone', $this->pbl_phone])
            ->andFilterWhere(['like', 'pbl_description', $this->pbl_description]);

        return $dataProvider;
    }
}
