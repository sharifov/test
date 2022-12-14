<?php

namespace modules\qaTask\src\entities\qaTask\search\object;

use modules\qaTask\src\entities\qaTask\search\QaTaskSearch;
use src\helpers\query\QueryHelper;
use yii\data\ActiveDataProvider;

/**
 * Class QaTaskObjectSearch
 */
class QaTaskObjectSearch extends QaTaskSearch
{
    public function rules(): array
    {
        return [
            ['t_id', 'integer'],

            ['t_status_id', 'integer'],
            ['t_status_id', 'in', 'range' => array_keys($this->getStatusList())],

            ['t_category_id', 'integer'],
            ['t_category_id', 'in', 'range' => array_keys($this->getCategoryList())],

            ['t_rating', 'integer'],
            ['t_rating', 'in', 'range' => array_keys($this->getRatingList())],

            ['t_description', 'string'],

            ['t_created_dt', 'date', 'format' => 'php:Y-m-d'],
            ['t_updated_dt', 'date', 'format' => 'php:Y-m-d'],

            ['t_assigned_user_id', 'integer'],
            ['t_assigned_user_id', 'in', 'range' => array_keys($this->getUserList())],
        ];
    }

    public function search(int $objectType, int $objectId, $params): ActiveDataProvider
    {
        $query = static::find()->with(['assignedUser', 'category']);

        $query->byObjectType($objectType);

        $query->byObjectId($objectId);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['t_id' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->t_created_dt) {
            QueryHelper::dayEqualByUserTZ($query, 't_created_dt', $this->t_created_dt, $this->user->timezone);
        }

        if ($this->t_updated_dt) {
            QueryHelper::dayEqualByUserTZ($query, 't_updated_dt', $this->t_updated_dt, $this->user->timezone);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            't_id' => $this->t_id,
            't_status_id' => $this->t_status_id,
            't_category_id' => $this->t_category_id,
            't_rating' => $this->t_rating,
            't_assigned_user_id' => $this->t_assigned_user_id,
        ]);

        $query->andFilterWhere(['like', 't_description', $this->t_description]);

        return $dataProvider;
    }
}
