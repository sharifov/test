<?php

namespace modules\qaTask\src\entities\qaTaskCategory\search;

use common\models\Employee;
use modules\qaTask\src\entities\QaObjectType;
use sales\helpers\query\QueryHelper;
use yii\data\ActiveDataProvider;
use modules\qaTask\src\entities\qaTaskCategory\QaTaskCategory;

class QaTaskCategoryCrudSearch extends QaTaskCategory
{
    public function rules(): array
    {
        return [
            ['tc_id', 'integer'],

            ['tc_key', 'string', 'max' => 30],

            ['tc_object_type_id', 'integer'],
            ['tc_object_type_id', 'in','range' => array_keys(QaObjectType::getList())],

            ['tc_name', 'string', 'max' => 30],

            ['tc_description', 'string', 'max' => 255],

            ['tc_enabled', 'boolean'],

            ['tc_default', 'boolean'],

            ['tc_created_user_id', 'integer'],
            ['tc_created_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['tc_created_user_id' => 'id']],

            ['tc_updated_user_id', 'integer'],
            ['tc_updated_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['tc_updated_user_id' => 'id']],

            ['tc_created_dt', 'date', 'format' => 'php:Y-m-d'],
            ['tc_updated_dt', 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public function search($params, Employee $user): ActiveDataProvider
    {
        $query = QaTaskCategory::find()->with(['createdUser', 'updatedUser']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->tc_created_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'tc_created_dt', $this->tc_created_dt, $user->timezone);
        }

        if ($this->tc_updated_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'tc_updated_dt', $this->tc_updated_dt, $user->timezone);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'tc_id' => $this->tc_id,
            'tc_object_type_id' => $this->tc_object_type_id,
            'tc_enabled' => $this->tc_enabled,
            'tc_default' => $this->tc_default,
            'tc_created_user_id' => $this->tc_created_user_id,
            'tc_updated_user_id' => $this->tc_updated_user_id,
        ]);

        $query->andFilterWhere(['like', 'tc_key', $this->tc_key])
            ->andFilterWhere(['like', 'tc_name', $this->tc_name])
            ->andFilterWhere(['like', 'tc_description', $this->tc_description]);

        return $dataProvider;
    }
}
