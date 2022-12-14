<?php

namespace modules\qaTask\src\entities\qaTask\search\queue;

use modules\qaTask\src\entities\qaTask\search\CreateDto;
use modules\qaTask\src\entities\qaTask\search\QaTaskSearch;
use modules\qaTask\src\entities\qaTaskStatus\QaTaskStatus;
use src\auth\Auth;
use src\helpers\query\QueryHelper;
use yii\data\ActiveDataProvider;

/**
 * Class QaTaskQueueProcessingSearch
 */
class QaTaskSearchProcessingSearch extends QaTaskSearch
{
    public $objectOwner;

    public static function createSearch(CreateDto $dto): QaTaskSearch
    {
        $dto->statusList = QaTaskStatus::getProcessingQueueList();
        return parent::createSearch($dto);
    }

    public function rules(): array
    {
        return [
            ['t_id', 'integer'],

            ['t_gid', 'string', 'max' => 32],

            ['t_project_id', 'integer'],
            ['t_project_id', 'in', 'range' => array_keys($this->getProjectList())],

            ['t_object_type_id', 'integer'],
            ['t_object_type_id', 'in', 'range' => array_keys($this->getObjectTypeList())],

            ['t_object_id', 'integer'],

            ['t_status_id', 'integer'],
            ['t_status_id', 'in', 'range' => array_keys($this->getStatusList())],

            ['t_category_id', 'integer'],
            ['t_category_id', 'in', 'range' => array_keys($this->getCategoryList())],

            ['t_rating', 'integer'],
            ['t_rating', 'in', 'range' => array_keys($this->getRatingList())],

            ['t_create_type_id', 'integer'],
            ['t_create_type_id', 'in', 'range' => array_keys($this->getCreatedTypeList())],

            ['t_department_id', 'integer'],
            ['t_department_id', 'in', 'range' => array_keys($this->getDepartmentList())],

            ['t_description', 'string'],

            ['t_deadline_dt', 'date', 'format' => 'php:Y-m-d'],
            ['t_created_dt', 'date', 'format' => 'php:Y-m-d'],
            ['t_updated_dt', 'date', 'format' => 'php:Y-m-d'],

            ['t_created_user_id', 'integer'],
            ['t_created_user_id', 'in', 'range' => array_keys($this->getUserList())],

            ['t_updated_user_id', 'integer'],
            ['t_updated_user_id', 'in', 'range' => array_keys($this->getUserList())],

            ['t_assigned_user_id', 'integer'],
            ['t_assigned_user_id', 'in', 'range' => array_keys($this->getUserList())],

            [['objectOwner'], 'integer'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find()->with(['createdUser', 'updatedUser', 'assignedUser', 'category', 'project']);

        $this->queryAccessService->processProject($this->user->getAccess(), $query);

        $query->statuses(array_keys($this->getStatusList()));

        $query->anyAssigned();

        if (Auth::can('qa-task/qa-task-queue/processing_Current')) {
            $query->assigned($this->user->id);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['t_updated_dt' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->objectOwner) {
            QueryHelper::getQaTasksByOwner($query, $this->objectOwner);
        }

        if ($this->t_created_dt) {
            QueryHelper::dayEqualByUserTZ($query, 't_created_dt', $this->t_created_dt, $this->user->timezone);
        }

        if ($this->t_updated_dt) {
            QueryHelper::dayEqualByUserTZ($query, 't_updated_dt', $this->t_updated_dt, $this->user->timezone);
        }

        if ($this->t_deadline_dt) {
            QueryHelper::dayEqualByUserTZ($query, 't_deadline_dt', $this->t_deadline_dt, $this->user->timezone);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            't_id' => $this->t_id,
            't_project_id' => $this->t_project_id,
            't_object_type_id' => $this->t_object_type_id,
            't_object_id' => $this->t_object_id,
            't_category_id' => $this->t_category_id,
            't_status_id' => $this->t_status_id,
            't_rating' => $this->t_rating,
            't_create_type_id' => $this->t_create_type_id,
            't_department_id' => $this->t_department_id,
            't_assigned_user_id' => $this->t_assigned_user_id,
            't_created_user_id' => $this->t_created_user_id,
            't_updated_user_id' => $this->t_updated_user_id,
        ]);

        $query->andFilterWhere(['like', 't_gid', $this->t_gid])
            ->andFilterWhere(['like', 't_description', $this->t_description]);

        return $dataProvider;
    }
}
