<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Email;

/**
 * EmailSearch represents the model behind the search form of `common\models\Email`.
 */
class EmailSearch extends Email
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['e_id', 'e_reply_id', 'e_lead_id', 'e_project_id', 'e_type_id', 'e_template_type_id', 'e_communication_id', 'e_is_deleted', 'e_is_new', 'e_delay', 'e_priority', 'e_status_id', 'e_created_user_id', 'e_updated_user_id'], 'integer'],
            [['e_email_from', 'e_email_to', 'e_email_cc', 'e_email_bc', 'e_email_subject', 'e_email_body_html', 'e_email_body_text', 'e_attach', 'e_email_data', 'e_language_id', 'e_status_done_dt', 'e_read_dt', 'e_error_message', 'e_created_dt', 'e_updated_dt'], 'safe'],
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
        $query = Email::find();

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
            'e_id' => $this->e_id,
            'e_reply_id' => $this->e_reply_id,
            'e_lead_id' => $this->e_lead_id,
            'e_project_id' => $this->e_project_id,
            'e_type_id' => $this->e_type_id,
            'e_template_type_id' => $this->e_template_type_id,
            'e_communication_id' => $this->e_communication_id,
            'e_is_deleted' => $this->e_is_deleted,
            'e_is_new' => $this->e_is_new,
            'e_delay' => $this->e_delay,
            'e_priority' => $this->e_priority,
            'e_status_id' => $this->e_status_id,
            'e_status_done_dt' => $this->e_status_done_dt,
            'e_read_dt' => $this->e_read_dt,
            'e_created_user_id' => $this->e_created_user_id,
            'e_updated_user_id' => $this->e_updated_user_id,
            'e_created_dt' => $this->e_created_dt,
            'e_updated_dt' => $this->e_updated_dt,
        ]);

        $query->andFilterWhere(['like', 'e_email_from', $this->e_email_from])
            ->andFilterWhere(['like', 'e_email_to', $this->e_email_to])
            ->andFilterWhere(['like', 'e_email_cc', $this->e_email_cc])
            ->andFilterWhere(['like', 'e_email_bc', $this->e_email_bc])
            ->andFilterWhere(['like', 'e_email_subject', $this->e_email_subject])
            ->andFilterWhere(['like', 'e_email_body_html', $this->e_email_body_html])
            ->andFilterWhere(['like', 'e_email_body_text', $this->e_email_body_text])
            ->andFilterWhere(['like', 'e_attach', $this->e_attach])
            ->andFilterWhere(['like', 'e_email_data', $this->e_email_data])
            ->andFilterWhere(['like', 'e_language_id', $this->e_language_id])
            ->andFilterWhere(['like', 'e_error_message', $this->e_error_message]);

        return $dataProvider;
    }
}
