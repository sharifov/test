<?php

namespace modules\qaTask\src\entities\qaTask\search\queue;

use common\models\Department;
use common\models\Employee;
use modules\qaTask\src\entities\QaObjectType;
use modules\qaTask\src\entities\qaTask\QaTaskCreatedType;
use modules\qaTask\src\entities\qaTask\QaTaskRating;
use modules\qaTask\src\entities\qaTaskCategory\QaTaskCategory;
use modules\qaTask\src\entities\qaTaskStatus\QaTaskStatus;
use sales\access\ListsAccess;
use sales\helpers\query\QueryHelper;
use yii\data\ActiveDataProvider;
use modules\qaTask\src\entities\qaTask\QaTask;

/**
 * Class QaTaskQueueProcessingSearch
 *
 * @property Employee $user
 * @property array $projects
 */
class QaTaskQueueProcessingSearch extends QaTask
{
    private $user;
    private $projects;

    public function __construct(Employee $user, $config = [])
    {
        $this->user = $user;
        $this->projects = (new ListsAccess($user->id))->getProjects();
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            ['t_id', 'integer'],

            ['t_gid', 'string', 'max' => 32],

            ['t_project_id', 'integer'],
            ['t_project_id', 'in', 'range' => array_keys($this->projects)],

            ['t_object_type_id', 'integer'],
            ['t_object_type_id', 'in', 'range' => array_keys(QaObjectType::getList())],

            ['t_object_id', 'integer'],

            ['t_status_id', 'integer'],
            ['t_status_id', 'in', 'range' => array_keys(QaTaskStatus::getProcessingQueueList())],

            ['t_category_id', 'integer'],
            ['t_category_id', 'exist', 'skipOnError' => true, 'targetClass' => QaTaskCategory::class, 'targetAttribute' => ['t_category_id' => 'tc_id']],

            ['t_rating', 'integer'],
            ['t_rating', 'in', 'range' => array_keys(QaTaskRating::getList())],

            ['t_create_type_id', 'integer'],
            ['t_create_type_id', 'in', 'range' => array_keys(QaTaskCreatedType::getList())],

            ['t_department_id', 'integer'],
            ['t_department_id', 'in', 'range' => array_keys(Department::DEPARTMENT_LIST)],

            ['t_description', 'string'],

            ['t_deadline_dt', 'date', 'format' => 'php:Y-m-d'],
            ['t_created_dt', 'date', 'format' => 'php:Y-m-d'],
            ['t_updated_dt', 'date', 'format' => 'php:Y-m-d'],

            ['t_created_user_id', 'integer'],
            ['t_created_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['t_created_user_id' => 'id']],

            ['t_updated_user_id', 'integer'],
            ['t_updated_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['t_updated_user_id' => 'id']],

            ['t_assigned_user_id', 'integer'],
            ['t_assigned_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['t_assigned_user_id' => 'id']],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = QaTask::find()->with(['createdUser', 'updatedUser', 'assignedUser', 'category', 'project']);

        $query->projects(array_keys($this->projects));

        $query->queueProcessing()->assigned();

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
