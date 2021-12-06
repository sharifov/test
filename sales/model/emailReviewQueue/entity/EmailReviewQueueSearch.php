<?php

namespace sales\model\emailReviewQueue\entity;

use common\models\Department;
use common\models\Email;
use common\models\Employee;
use common\models\Project;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * EmailReviewQueueSearch represents the model behind the search form of `sales\model\emailReviewQueue\entity\EmailReviewQueue`.
 */
class EmailReviewQueueSearch extends EmailReviewQueue
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['erq_id', 'erq_email_id', 'erq_project_id', 'erq_department_id', 'erq_owner_id', 'erq_status_id', 'erq_user_reviewer_id'], 'integer'],
            [['erq_created_dt', 'erq_updated_dt'], 'safe'],
            [['erq_status_id'], 'in', 'range' => array_keys(EmailReviewQueueStatus::getList())],
            [['erq_department_id'], 'exist', 'skipOnError' => true, 'targetClass' => Department::class, 'targetAttribute' => ['erq_department_id' => 'dep_id']],
            [['erq_email_id'], 'exist', 'skipOnError' => true, 'targetClass' => Email::class, 'targetAttribute' => ['erq_email_id' => 'e_id']],
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
            'erq_created_dt' => $this->erq_created_dt,
            'erq_updated_dt' => $this->erq_updated_dt,
        ]);

        return $dataProvider;
    }

    public function reviewQueue(array $params, Employee $user): ActiveDataProvider
    {
        $query = EmailReviewQueue::find();

        $query->with('erqEmail');


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

        $query->andFilterWhere([
            'erq_id' => $this->erq_id,
            'erq_email_id' => $this->erq_email_id,
            'erq_owner_id' => $this->erq_owner_id,
            'erq_status_id' => $this->erq_status_id,
            'erq_user_reviewer_id' => $this->erq_user_reviewer_id,
            'erq_created_dt' => $this->erq_created_dt,
            'erq_updated_dt' => $this->erq_updated_dt,
        ]);

        return $dataProvider;
    }
}
