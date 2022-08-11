<?php

namespace src\model\emailReviewQueue\entity;

use common\models\Department;
use common\models\Email;
use common\models\Employee;
use common\models\Project;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * EmailReviewQueueSearch represents the model behind the search form of `src\model\emailReviewQueue\entity\EmailReviewQueue`.
 */
class EmailReviewQueueSearch extends EmailReviewQueue
{
    private const SCENARIO_PENDING_STATUSES = 'pendingStatuses';
    private const SCENARIO_COMPLETED_STATUSES = 'completedStatuses';

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['erq_id', 'erq_email_id', 'erq_project_id', 'erq_department_id', 'erq_owner_id', 'erq_status_id', 'erq_user_reviewer_id'], 'integer'],
            [['erq_created_dt', 'erq_updated_dt'], 'safe'],
            [['erq_status_id'], 'in', 'range' => array_keys(EmailReviewQueueStatus::getList()), 'on' => self::SCENARIO_DEFAULT],
            [['erq_status_id'], 'in', 'range' => array_keys(EmailReviewQueueStatus::getPendingList()), 'on' => self::SCENARIO_PENDING_STATUSES],
            [['erq_status_id'], 'in', 'range' => array_keys(EmailReviewQueueStatus::getCompletedList()), 'on' => self::SCENARIO_COMPLETED_STATUSES],
            [['erq_department_id'], 'exist', 'skipOnError' => true, 'targetClass' => Department::class, 'targetAttribute' => ['erq_department_id' => 'dep_id']],
            [['erq_email_id'], 'exist', 'skipOnError' => true, 'targetRelation' => 'email'],
            [['erq_owner_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['erq_owner_id' => 'id']],
            [['erq_project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['erq_project_id' => 'id']],
            [['erq_user_reviewer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['erq_user_reviewer_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = EmailReviewQueue::find();

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
            'erq_id' => $this->erq_id,
            'erq_email_id' => $this->erq_email_id,
            'erq_project_id' => $this->erq_project_id,
            'erq_department_id' => $this->erq_department_id,
            'erq_owner_id' => $this->erq_owner_id,
            'erq_status_id' => $this->erq_status_id,
            'erq_user_reviewer_id' => $this->erq_user_reviewer_id,
            'date(erq_created_dt)' => $this->erq_created_dt,
            'date(erq_updated_dt)' => $this->erq_updated_dt,
        ]);

        return $dataProvider;
    }

    public function reviewQueue(array $params, Employee $user): ActiveDataProvider
    {
        $query = $this->getQuery();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'erq_id' => SORT_DESC
                ]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        if (!$user->isSuperAdmin() && !$user->isOnlyAdmin()) {
            $query->join('join', Project::tableName(), 'id = erq_project_id');
            $query->join('join', Department::tableName(), 'dep_id = erq_department_id');
        }

        $this->applyDefaultFilters($query);
        $query->andFilterWhere(['erq_status_id' => $this->erq_status_id]);

        return $dataProvider;
    }

    public function reviewQueueByStatuses(array $params, Employee $user, array $statuses): ActiveDataProvider
    {
        $query = $this->getQuery();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'erq_id' => SORT_DESC
                ]
            ]
        ]);

        $this->load($params);

        if ($this->erq_status_id) {
            $query->andFilterWhere([
                'erq_status_id' => $this->erq_status_id
            ]);
        } else {
            $query->filterByStatuses($statuses);
        }

        if (!$this->validate()) {
            return $dataProvider;
        }

        if (!$user->isSuperAdmin() && !$user->isOnlyAdmin()) {
            $query->join('join', Project::tableName(), 'id = erq_project_id');
            $query->join('join', Department::tableName(), 'dep_id = erq_department_id');
        }

        $this->applyDefaultFilters($query);

        return $dataProvider;
    }

    private function getQuery(): Scopes
    {
        $query = EmailReviewQueue::find();
        $query->with('erqEmail');
        return $query;
    }

    private function applyDefaultFilters(Scopes $query): Scopes
    {
        $query->andFilterWhere([
            'erq_id' => $this->erq_id,
            'erq_email_id' => $this->erq_email_id,
            'erq_owner_id' => $this->erq_owner_id,
            'erq_user_reviewer_id' => $this->erq_user_reviewer_id,
            'date(erq_created_dt)' => $this->erq_created_dt,
            'date(erq_updated_dt)' => $this->erq_updated_dt,
            'erq_project_id' => $this->erq_project_id,
            'erq_department_id' => $this->erq_department_id
        ]);
        return $query;
    }

    public function setPendingScenario(): void
    {
        $this->scenario = self::SCENARIO_PENDING_STATUSES;
    }

    public function setCompletedScenario(): void
    {
        $this->scenario = self::SCENARIO_COMPLETED_STATUSES;
    }
}
