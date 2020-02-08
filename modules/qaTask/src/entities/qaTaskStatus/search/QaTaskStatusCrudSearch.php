<?php

namespace modules\qaTask\src\entities\qaTaskStatus\search;

use common\models\Employee;
use sales\helpers\query\QueryHelper;
use yii\data\ActiveDataProvider;
use modules\qaTask\src\entities\qaTaskStatus\QaTaskStatus;

class QaTaskStatusCrudSearch extends QaTaskStatus
{
    public function rules(): array
    {
        return [
            ['ts_id', 'integer'],

            ['ts_name', 'string', 'max' => 30],

            ['ts_description', 'string', 'max' => 255],

            ['ts_enabled', 'boolean'],

            ['ts_css_class', 'string', 'max' => 100],

            ['ts_created_user_id', 'integer'],
            ['ts_created_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['ts_created_user_id' => 'id']],

            ['ts_updated_user_id', 'integer'],
            ['ts_updated_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['ts_updated_user_id' => 'id']],

            ['ts_created_dt', 'date', 'format' => 'php:Y-m-d'],
            ['ts_updated_dt', 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public function search($params, Employee $user): ActiveDataProvider
    {
        $query = QaTaskStatus::find()->with(['createdUser', 'updatedUser']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->ts_created_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'ts_created_dt', $this->ts_created_dt, $user->timezone);
        }

        if ($this->ts_updated_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'ts_updated_dt', $this->ts_updated_dt, $user->timezone);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'ts_id' => $this->ts_id,
            'ts_enabled' => $this->ts_enabled,
            'ts_created_user_id' => $this->ts_created_user_id,
            'ts_updated_user_id' => $this->ts_updated_user_id,
        ]);

        $query->andFilterWhere(['like', 'ts_name', $this->ts_name])
            ->andFilterWhere(['like', 'ts_description', $this->ts_description])
            ->andFilterWhere(['like', 'ts_css_class', $this->ts_css_class]);

        return $dataProvider;
    }
}
