<?php

namespace modules\qaTask\src\entities\qaTaskStatusReason\search;

use common\models\Employee;
use modules\qaTask\src\entities\QaObjectType;
use modules\qaTask\src\entities\qaTaskStatus\QaTaskStatus;
use sales\helpers\query\QueryHelper;
use yii\data\ActiveDataProvider;
use modules\qaTask\src\entities\qaTaskStatusReason\QaTaskStatusReason;

class QaTaskStatusReasonCrudSearch extends QaTaskStatusReason
{
    public function rules(): array
    {
        return [
            ['tsr_object_type_id', 'integer'],
            ['tsr_object_type_id', 'in', 'range' => array_keys(QaObjectType::getList())],

            ['tsr_status_id', 'integer'],
            ['tsr_status_id', 'in', 'range' => array_keys(QaTaskStatus::getList())],

            ['tsr_key', 'string', 'max' => 30],

            ['tsr_name', 'string', 'max' => 30],

            ['tsr_description', 'string', 'max' => 255],

            ['tsr_comment_required', 'boolean'],

            ['tsr_enabled', 'boolean'],

            ['tsr_created_user_id', 'integer'],
            ['tsr_created_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['tsr_created_user_id' => 'id']],

            ['tsr_updated_user_id', 'integer'],
            ['tsr_updated_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['tsr_updated_user_id' => 'id']],

            ['tsr_created_dt', 'date', 'format' => 'php:Y-m-d'],
            ['tsr_updated_dt', 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public function search($params, Employee $user): ActiveDataProvider
    {
        $query = QaTaskStatusReason::find()->with(['createdUser', 'updatedUser']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->tsr_created_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'tsr_created_dt', $this->tsr_created_dt, $user->timezone);
        }

        if ($this->tsr_updated_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'tsr_updated_dt', $this->tsr_updated_dt, $user->timezone);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'tsr_id' => $this->tsr_id,
            'tsr_object_type_id' => $this->tsr_object_type_id,
            'tsr_status_id' => $this->tsr_status_id,
            'tsr_comment_required' => $this->tsr_comment_required,
            'tsr_enabled' => $this->tsr_enabled,
            'tsr_created_user_id' => $this->tsr_created_user_id,
            'tsr_updated_user_id' => $this->tsr_updated_user_id,
        ]);

        $query->andFilterWhere(['like', 'tsr_key', $this->tsr_key])
            ->andFilterWhere(['like', 'tsr_name', $this->tsr_name])
            ->andFilterWhere(['like', 'tsr_description', $this->tsr_description]);

        return $dataProvider;
    }
}
