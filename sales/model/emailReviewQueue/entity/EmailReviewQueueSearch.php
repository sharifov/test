<?php

namespace sales\model\emailReviewQueue\entity;

use yii\base\Model;
use yii\data\ActiveDataProvider;

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
}
