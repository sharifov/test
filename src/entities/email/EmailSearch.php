<?php

namespace src\entities\email;

use common\models\EmailTemplateType;
use common\models\Employee;
use common\models\UserGroupAssign;
use src\auth\Auth;
use src\helpers\query\QueryHelper;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

/**
 * EmailSearch
 *
 * @property string $e_template_type_name
 */
class EmailSearch extends Email
{
    public $email_type_id;
    public $supervision_id;
    public $e_template_type_name;

    public $datetime_start;
    public $datetime_end;
    public $date_range;
    public const CREATE_TIME_START_DEFAULT_RANGE = '-6 days';

    /**
     * {@inheritdoc}
     */
   /*  public function rules()
    {
        return [
            [['datetime_start', 'datetime_end'], 'safe'],
            [['date_range'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
            [['e_id', 'e_project_id', 'e_type_id', 'e_is_deleted', 'e_status_id', 'e_created_user_id',  'email_type_id', 'supervision_id'], 'integer'],
            //[['e_template_type_name'], 'string'],
           // [['e_email_from', 'e_email_to', 'e_email_cc', 'e_email_bc', 'e_email_subject', 'e_email_body_text', 'e_attach', 'e_email_data', 'e_language_id', 'e_status_done_dt', 'e_read_dt', 'e_error_message', 'e_message_id', 'e_ref_message_id', 'e_inbox_created_dt'], 'safe'],
           // [['e_created_dt', 'e_updated_dt'], 'date', 'format' => 'php:Y-m-d'],
        ];
    } */

    public function __construct($config = [])
    {
        parent::__construct($config);
        $userTimezone = Auth::user()->userParams->up_timezone ?? 'UTC';
        $currentDate = (new \DateTimeImmutable('now', new \DateTimeZone('UTC')))->setTimezone(new \DateTimeZone($userTimezone));
        $this->date_range = ($currentDate->modify(self::CREATE_TIME_START_DEFAULT_RANGE))->format('Y-m-d') . ' 00:00:00 - ' . $currentDate->format('Y-m-d') . ' 23:59:59';
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
        $query = self::find()->with('createdUser', 'project', 'templateType');

        $query->addSelect([
            'e_id',
            //'e_lead_id',
            //'e_case_id',
            'e_project_id',
            //'e_email_from',
            //'e_email_to',
            'e_type_id',
           // 'e_template_type_id',
           // 'e_language_id',
           // 'e_communication_id',
            'e_status_id',
            'e_created_user_id',
            'e_created_dt',
            //'e_client_id'
        ]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['e_id' => SORT_DESC]],
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

        if ($this->datetime_start && $this->datetime_end) {
            $query->andFilterWhere(['>=', 'e_created_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->datetime_start))])
            ->andFilterWhere(['<=', 'e_created_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->datetime_end))]);
        }

        if (!empty($this->e_created_dt)) {
            $query->andFilterWhere(['DATE(e_created_dt)' => date('Y-m-d', strtotime($this->e_created_dt))]);
        }

        if ($this->supervision_id > 0) {
            $subQuery1 = UserGroupAssign::find()->select(['ugs_group_id'])->where(['ugs_user_id' => $this->supervision_id]);
            $subQuery = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])->where(['IN', 'ugs_group_id', $subQuery1]);
            $query->andWhere(['IN', 'e_created_user_id', $subQuery]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'e_id' => $this->e_id,
            //'e_reply_id' => $this->e_reply_id,
            //'e_lead_id' => $this->e_lead_id,
            //'e_case_id' => $this->e_case_id,
            'e_project_id' => $this->e_project_id,
            'e_type_id' => $this->e_type_id,
            //            'e_template_type_id' => $this->e_template_type_id,
           // 'e_communication_id' => $this->e_communication_id,
            'e_is_deleted' => $this->e_is_deleted,
           // 'e_is_new' => $this->e_is_new,
            //'e_delay' => $this->e_delay,
            //'e_priority' => $this->e_priority,
            'e_status_id' => $this->e_status_id,
           // 'e_status_done_dt' => $this->e_status_done_dt,
           // 'e_read_dt' => $this->e_read_dt,
            'e_created_user_id' => $this->e_created_user_id,
           // 'e_updated_user_id' => $this->e_updated_user_id,
           // 'e_updated_dt' => $this->e_updated_dt,
           // 'e_inbox_created_dt' => $this->e_inbox_created_dt,
           // 'e_inbox_email_id' => $this->e_inbox_email_id,
           // 'e_email_from' => $this->e_email_from,
           // 'e_email_to' => $this->e_email_to,
           // 'e_language_id' => $this->e_language_id,
            //'e_client_id' => $this->e_client_id,
        ]);

        if ($this->e_template_type_name) {
            $templateIds = EmailTemplateType::find()->select(['DISTINCT(etp_id) as e_template_type_id'])->where(['like', 'etp_name', $this->e_template_type_name])->asArray()->all();
            if ($templateIds) {
                $query->andFilterWhere(['e_template_type_id' => $templateIds]);
            }
        }

       /*  $query
        ->andFilterWhere(['like', 'e_email_cc', $this->e_email_cc])
        ->andFilterWhere(['like', 'e_email_bc', $this->e_email_bc])
        ->andFilterWhere(['like', 'e_email_subject', $this->e_email_subject])
        ->andFilterWhere(['like', 'e_email_body_text', $this->e_email_body_text])
        ->andFilterWhere(['like', 'e_attach', $this->e_attach])
        ->andFilterWhere(['like', 'e_email_data', $this->e_email_data])
        ->andFilterWhere(['like', 'e_error_message', $this->e_error_message])
        ->andFilterWhere(['like', 'e_message_id', $this->e_message_id])
        ->andFilterWhere(['like', 'e_ref_message_id', $this->e_ref_message_id]); */

        $dataProvider->totalCount = $query->count('distinct e_id');

        return $dataProvider;
    }
}
