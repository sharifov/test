<?php

namespace modules\qaTask\src\entities\qaTaskRules\search;

use common\models\Employee;
use modules\qaTask\src\entities\qaTask\QaTaskObjectType;
use sales\helpers\query\QueryHelper;
use yii\data\ActiveDataProvider;
use modules\qaTask\src\entities\qaTaskRules\QaTaskRules;

class QaTaskRulesSearch extends QaTaskRules
{
    public function rules(): array
    {
        return [
            ['tr_id', 'integer'],

            ['tr_key', 'string', 'max' => 30],

            ['tr_type', 'integer'],
            ['tr_type', 'in', 'range' => array_keys(QaTaskObjectType::getList())],

            ['tr_name', 'string', 'max' => 50],

            ['tr_description', 'string', 'max' => 255],

            ['tr_parameters', 'string'],

            ['tr_enabled', 'boolean'],

            ['tr_created_dt', 'date', 'format' => 'php:Y-m-d'],
            ['tr_updated_dt', 'date', 'format' => 'php:Y-m-d'],

            ['tr_created_user_id', 'exist', 'targetClass' => Employee::class, 'targetAttribute' => ['tr_created_user_id' => 'id']],
            ['tr_updated_user_id', 'exist', 'targetClass' => Employee::class, 'targetAttribute' => ['tr_updated_user_id' => 'id']],
        ];
    }

    public function search($params, Employee $user): ActiveDataProvider
    {
        $query = QaTaskRules::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->tr_created_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'tr_created_dt', $this->tr_created_dt, $user->timezone);
        }

        if ($this->tr_updated_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'tr_updated_dt', $this->tr_updated_dt, $user->timezone);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'tr_id' => $this->tr_id,
            'tr_type' => $this->tr_type,
            'tr_enabled' => $this->tr_enabled,
            'tr_created_user_id' => $this->tr_created_user_id,
            'tr_updated_user_id' => $this->tr_updated_user_id,
        ]);

        $query->andFilterWhere(['like', 'tr_key', $this->tr_key])
            ->andFilterWhere(['like', 'tr_name', $this->tr_name])
            ->andFilterWhere(['like', 'tr_description', $this->tr_description])
            ->andFilterWhere(['like', 'tr_parameters', $this->tr_parameters]);

        return $dataProvider;
    }
}
