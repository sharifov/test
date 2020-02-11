<?php

namespace modules\qaTask\src\entities\qaTaskActionReason\search;

use common\models\Employee;
use modules\qaTask\src\entities\QaObjectType;
use modules\qaTask\src\useCases\qaTask\QaTaskActions;
use sales\helpers\query\QueryHelper;
use yii\data\ActiveDataProvider;
use modules\qaTask\src\entities\qaTaskActionReason\QaTaskActionReason;

class QaTaskActionReasonCrudSearch extends QaTaskActionReason
{
    public function rules(): array
    {
        return [
            ['tar_object_type_id', 'integer'],
            ['tar_object_type_id', 'in', 'range' => array_keys(QaObjectType::getList())],

            ['tar_action_id', 'integer'],
            ['tar_action_id', 'in', 'range' => array_keys(QaTaskActions::getList())],

            ['tar_key', 'string', 'max' => 30],

            ['tar_name', 'string', 'max' => 30],

            ['tar_description', 'string', 'max' => 255],

            ['tar_comment_required', 'boolean'],

            ['tar_enabled', 'boolean'],

            ['tar_created_user_id', 'integer'],
            ['tar_created_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['tar_created_user_id' => 'id']],

            ['tar_updated_user_id', 'integer'],
            ['tar_updated_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['tar_updated_user_id' => 'id']],

            ['tar_created_dt', 'date', 'format' => 'php:Y-m-d'],
            ['tar_updated_dt', 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public function search($params, Employee $user): ActiveDataProvider
    {
        $query = QaTaskActionReason::find()->with(['createdUser', 'updatedUser']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->tar_created_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'tar_created_dt', $this->tar_created_dt, $user->timezone);
        }

        if ($this->tar_updated_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'tar_updated_dt', $this->tar_updated_dt, $user->timezone);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'tar_id' => $this->tar_id,
            'tar_object_type_id' => $this->tar_object_type_id,
            'tar_action_id' => $this->tar_action_id,
            'tar_comment_required' => $this->tar_comment_required,
            'tar_enabled' => $this->tar_enabled,
            'tar_created_user_id' => $this->tar_created_user_id,
            'tar_updated_user_id' => $this->tar_updated_user_id,
        ]);

        $query->andFilterWhere(['like', 'tar_key', $this->tar_key])
            ->andFilterWhere(['like', 'tar_name', $this->tar_name])
            ->andFilterWhere(['like', 'tar_description', $this->tar_description]);

        return $dataProvider;
    }
}
