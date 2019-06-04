<?php

namespace common\models\search;

use common\models\UserGroupAssign;
use common\models\UserProjectParams;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Email;

/**
 * EmailSearch represents the model behind the search form of `common\models\Email`.
 */
class EmailSearch extends Email
{

    public $email_type_id;
    public $supervision_id;

    public $datetime_start;
    public $datetime_end;
    public $date_range;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['datetime_start', 'datetime_end'], 'safe'],
            [['date_range'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
            [['e_id', 'e_reply_id', 'e_lead_id', 'e_project_id', 'e_type_id', 'e_template_type_id', 'e_communication_id', 'e_is_deleted', 'e_is_new', 'e_delay', 'e_priority', 'e_status_id', 'e_created_user_id', 'e_updated_user_id', 'e_inbox_email_id', 'email_type_id', 'supervision_id'], 'integer'],
            [['e_email_from', 'e_email_to', 'e_email_cc', 'e_email_bc', 'e_email_subject', 'e_email_body_html', 'e_email_body_text', 'e_attach', 'e_email_data', 'e_language_id', 'e_status_done_dt', 'e_read_dt', 'e_error_message', 'e_created_dt', 'e_updated_dt', 'e_message_id', 'e_ref_message_id', 'e_inbox_created_dt'], 'safe'],
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
        $query = Email::find()->with('eCreatedUser', 'eProject', 'eTemplateType');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['e_id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if(empty($this->e_created_dt) && isset($params['EmailSearch']['date_range'])){
            $query->andFilterWhere(['>=', 'DATE(e_created_dt)', $this->datetime_start])
                ->andFilterWhere(['<=', 'DATE(e_created_dt)', $this->datetime_end]);
        } elseif (isset($params['EmailSearch']['e_created_dt'])) {
            $query->andFilterWhere(['=','DATE(e_created_dt)', $this->e_created_dt]);
        }

        if($this->supervision_id > 0) {
            $subQuery1 = UserGroupAssign::find()->select(['ugs_group_id'])->where(['ugs_user_id' => $this->supervision_id]);
            $subQuery = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])->where(['IN', 'ugs_group_id', $subQuery1]);
            $query->andWhere(['IN', 'e_created_user_id', $subQuery]);
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
            'e_updated_dt' => $this->e_updated_dt,
            'e_inbox_created_dt' => $this->e_inbox_created_dt,
            'e_inbox_email_id' => $this->e_inbox_email_id,
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
            ->andFilterWhere(['like', 'e_error_message', $this->e_error_message])
            ->andFilterWhere(['like', 'e_message_id', $this->e_message_id])
            ->andFilterWhere(['like', 'e_ref_message_id', $this->e_ref_message_id]);

        return $dataProvider;
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function searchEmails($params)
    {
        $query = Email::find();

        // add conditions that should always apply here


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['e_created_dt' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 12,
            ],
        ]);

        //var_dump($params);        exit;

        $this->load($params);

        if(isset($params['email_type_id']) && $params['email_type_id'] > 0) {

            //echo $this->email_type_id; exit;

            $this->email_type_id = (int) $params['email_type_id'];

            if($this->email_type_id == Email::FILTER_TYPE_ALL) {
                $query->where(['e_is_deleted' => false]);
            }
            elseif($this->email_type_id == Email::FILTER_TYPE_INBOX) {
               $query->where(['e_type_id' => Email::TYPE_INBOX, 'e_is_deleted' => false]);
            }
            elseif($this->email_type_id == Email::FILTER_TYPE_OUTBOX) {
                $query->where(['e_type_id' => Email::TYPE_OUTBOX, 'e_is_deleted' => false]);
            }
            elseif($this->email_type_id == Email::FILTER_TYPE_DRAFT) {
                $query->where(['e_type_id' => Email::TYPE_DRAFT, 'e_is_deleted' => false]);
            }
            elseif($this->email_type_id == Email::FILTER_TYPE_TRASH) {
                $query->where(['e_is_deleted' => true]);
            }
        }



        if(isset($params['EmailSearch']['user_id']) && $params['EmailSearch']['user_id'] > 0) {
            $subQuery = UserProjectParams::find()->select(['upp_email'])->where(['upp_user_id' => $params['EmailSearch']['user_id']])->andWhere(['!=', 'upp_email', '']);
            $query->andWhere(['or', ['=', 'e_created_user_id', $params['EmailSearch']['user_id']], ['IN', 'e_email_from', $subQuery], ['and', ['IN', 'e_email_to', $subQuery], ['e_type_id' => Email::TYPE_INBOX]]]);
        }


        if(isset($params['EmailSearch']['email']) && $params['EmailSearch']['email']) {
            $params['EmailSearch']['email'] = strtolower(trim($params['EmailSearch']['email']));
            $query->andWhere(['or', ['e_email_from' => $params['EmailSearch']['email']], ['and', ['e_email_to' => $params['EmailSearch']['email']], ['e_type_id' => Email::TYPE_INBOX]]]);
        }





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
            'e_inbox_created_dt' => $this->e_inbox_created_dt,
            'e_inbox_email_id' => $this->e_inbox_email_id,
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
            ->andFilterWhere(['like', 'e_error_message', $this->e_error_message])
            ->andFilterWhere(['like', 'e_message_id', $this->e_message_id])
            ->andFilterWhere(['like', 'e_ref_message_id', $this->e_ref_message_id]);

        return $dataProvider;
    }
}
