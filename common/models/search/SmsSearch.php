<?php

namespace common\models\search;

use common\models\Employee;
use common\models\UserGroupAssign;
use common\models\UserProjectParams;
use sales\auth\Auth;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Sms;
use yii\db\Query;
use yii\helpers\VarDumper;

/**
 * SmsSearch represents the model behind the search form of `common\models\Sms`.
 */
class SmsSearch extends Sms
{
    public $supervision_id;

    public $datetime_start;
    public $datetime_end;
    public $date_range;
    public const CREATE_TIME_START_DEFAULT_RANGE = '-6 days';
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['datetime_start', 'datetime_end'], 'safe'],
            [['date_range'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
            [['s_id', 's_reply_id', 's_lead_id', 's_case_id', 's_project_id', 's_type_id', 's_template_type_id', 's_communication_id', 's_is_deleted', 's_is_new', 's_delay', 's_priority', 's_status_id', 's_tw_num_segments', 's_created_user_id', 's_updated_user_id', 'supervision_id'], 'integer'],
            [['s_phone_from', 's_phone_to', 's_sms_text', 's_sms_data', 's_language_id', 's_status_done_dt', 's_read_dt', 's_error_message', 's_tw_sent_dt', 's_tw_account_sid', 's_tw_message_sid', 's_tw_to_country', 's_tw_to_state', 's_tw_to_city', 's_tw_to_zip', 's_tw_from_country', 's_tw_from_state', 's_tw_from_city', 's_tw_from_zip', 's_created_dt', 's_updated_dt'], 'safe'],
            [['s_tw_price'], 'number'],
        ];
    }

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
        $query = Sms::find()->with('sCreatedUser', 'sProject');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['s_id' => SORT_DESC]
            ],
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


        if($this->datetime_start && $this->datetime_end){
            $query->andFilterWhere(['>=', 's_created_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->datetime_start))])
                ->andFilterWhere(['<=', 's_created_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->datetime_end))]);
        }

        if (!empty($this->s_created_dt)) {
            $query->andFilterWhere(['DATE(s_created_dt)' => date('Y-m-d', strtotime($this->e_created_dt))]);
        }

        if($this->supervision_id > 0) {
            $subQuery1 = UserGroupAssign::find()->select(['ugs_group_id'])->where(['ugs_user_id' => $this->supervision_id]);
            $subQuery = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])->where(['IN', 'ugs_group_id', $subQuery1]);
            $query->andWhere(['IN', 's_created_user_id', $subQuery]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            's_id' => $this->s_id,
            's_reply_id' => $this->s_reply_id,
            's_lead_id' => $this->s_lead_id,
            's_case_id' => $this->s_case_id,
            's_project_id' => $this->s_project_id,
            's_type_id' => $this->s_type_id,
            's_template_type_id' => $this->s_template_type_id,
            's_communication_id' => $this->s_communication_id,
            's_is_deleted' => $this->s_is_deleted,
            's_is_new' => $this->s_is_new,
            's_delay' => $this->s_delay,
            's_priority' => $this->s_priority,
            's_status_id' => $this->s_status_id,
            's_status_done_dt' => $this->s_status_done_dt,
            's_read_dt' => $this->s_read_dt,
            's_tw_price' => $this->s_tw_price,
            's_tw_sent_dt' => $this->s_tw_sent_dt,
            's_tw_num_segments' => $this->s_tw_num_segments,
            's_created_user_id' => $this->s_created_user_id,
            's_updated_user_id' => $this->s_updated_user_id,
            's_updated_dt' => $this->s_updated_dt,
        ]);

        $query->andFilterWhere(['like', 's_phone_from', $this->s_phone_from])
            ->andFilterWhere(['like', 's_phone_to', $this->s_phone_to])
            ->andFilterWhere(['like', 's_sms_text', $this->s_sms_text])
            ->andFilterWhere(['like', 's_sms_data', $this->s_sms_data])
            ->andFilterWhere(['like', 's_language_id', $this->s_language_id])
            ->andFilterWhere(['like', 's_error_message', $this->s_error_message])
            ->andFilterWhere(['like', 's_tw_account_sid', $this->s_tw_account_sid])
            ->andFilterWhere(['like', 's_tw_message_sid', $this->s_tw_message_sid])
            ->andFilterWhere(['like', 's_tw_to_country', $this->s_tw_to_country])
            ->andFilterWhere(['like', 's_tw_to_state', $this->s_tw_to_state])
            ->andFilterWhere(['like', 's_tw_to_city', $this->s_tw_to_city])
            ->andFilterWhere(['like', 's_tw_to_zip', $this->s_tw_to_zip])
            ->andFilterWhere(['like', 's_tw_from_country', $this->s_tw_from_country])
            ->andFilterWhere(['like', 's_tw_from_state', $this->s_tw_from_state])
            ->andFilterWhere(['like', 's_tw_from_city', $this->s_tw_from_city])
            ->andFilterWhere(['like', 's_tw_from_zip', $this->s_tw_from_zip]);

//        VarDumper::dump($params);
//        VarDumper::dump($query->createCommand()->getRawSql());die;

        return $dataProvider;
    }


    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchSms($params)
    {
        $query = Sms::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['s_created_dt' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if (isset($params['SmsSearch']['s_created_dt'])) {
            $query->andFilterWhere(['=','DATE(s_created_dt)', $this->s_created_dt]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            's_id' => $this->s_id,
            's_reply_id' => $this->s_reply_id,
            's_lead_id' => $this->s_lead_id,
//            's_case_id' => $this->s_case_id,
            's_project_id' => $this->s_project_id,
            's_type_id' => $this->s_type_id,
            's_template_type_id' => $this->s_template_type_id,
            's_communication_id' => $this->s_communication_id,
            's_is_deleted' => $this->s_is_deleted,
            's_is_new' => $this->s_is_new,
            's_delay' => $this->s_delay,
            's_priority' => $this->s_priority,
            's_status_id' => $this->s_status_id,
            's_status_done_dt' => $this->s_status_done_dt,
            's_read_dt' => $this->s_read_dt,
            's_tw_price' => $this->s_tw_price,
            's_tw_sent_dt' => $this->s_tw_sent_dt,
            's_tw_num_segments' => $this->s_tw_num_segments,
            's_created_user_id' => $this->s_created_user_id,
            's_updated_user_id' => $this->s_updated_user_id,
            's_updated_dt' => $this->s_updated_dt,
        ]);


        if(isset($params['SmsSearch']['user_id']) && $params['SmsSearch']['user_id'] > 0) {

//            $subQuery = UserProjectParams::find()->select(['DISTINCT(upp_tw_phone_number)'])->where(['upp_user_id' => $params['SmsSearch']['user_id']])
//                ->andWhere(['and', ['<>', 'upp_tw_phone_number', ''], ['IS NOT', 'upp_tw_phone_number', null]]);

            $subQuery = UserProjectParams::find()->select(['pl_phone_number'])->distinct()->byUserId($params['SmsSearch']['user_id'])->innerJoinWith('phoneList', false);

            //$subQuery2 = UserProjectParams::find()->select(['DISTINCT(upp_phone_number)'])->where(['upp_user_id' => $params['SmsSearch']['user_id']])
            //    ->andWhere(['and', ['<>', 'upp_phone_number', ''], ['IS NOT', 'upp_phone_number', null]]);

            $query->andWhere(['or', ['IN', 's_phone_from', $subQuery], ['and', ['IN', 's_phone_to', $subQuery], ['s_type_id' => Sms::TYPE_INBOX]]]);
            //$query->orWhere(['or', ['IN', 's_phone_from', $subQuery2], ['and', ['IN', 's_phone_to', $subQuery2], ['s_type_id' => Sms::TYPE_INBOX]]]);
        }


        if(isset($params['SmsSearch']['phone_list']) && $params['SmsSearch']['phone_list']) {
            //$params['SmsSearch']['phone_list'] = strtolower(trim($params['SmsSearch']['phone']));
            $query->andWhere(['or', ['s_phone_from' => $params['SmsSearch']['phone_list']], ['and', ['s_phone_to' => $params['SmsSearch']['phone_list']], ['s_type_id' => Sms::TYPE_INBOX]]]);
        }

        if(is_numeric($this->s_is_deleted)) {
            $query->andWhere(['s_is_deleted' => $this->s_is_deleted]);
        }

        $query->andFilterWhere(['like', 's_phone_from', $this->s_phone_from])
            ->andFilterWhere(['like', 's_phone_to', $this->s_phone_to])
            ->andFilterWhere(['like', 's_sms_text', $this->s_sms_text])
            ->andFilterWhere(['like', 's_sms_data', $this->s_sms_data])
            ->andFilterWhere(['like', 's_language_id', $this->s_language_id])
            ->andFilterWhere(['like', 's_error_message', $this->s_error_message])
            ->andFilterWhere(['like', 's_tw_account_sid', $this->s_tw_account_sid])
            ->andFilterWhere(['like', 's_tw_message_sid', $this->s_tw_message_sid])
            ->andFilterWhere(['like', 's_tw_to_country', $this->s_tw_to_country])
            ->andFilterWhere(['like', 's_tw_to_state', $this->s_tw_to_state])
            ->andFilterWhere(['like', 's_tw_to_city', $this->s_tw_to_city])
            ->andFilterWhere(['like', 's_tw_to_zip', $this->s_tw_to_zip])
            ->andFilterWhere(['like', 's_tw_from_country', $this->s_tw_from_country])
            ->andFilterWhere(['like', 's_tw_from_state', $this->s_tw_from_state])
            ->andFilterWhere(['like', 's_tw_from_city', $this->s_tw_from_city])
            ->andFilterWhere(['like', 's_tw_from_zip', $this->s_tw_from_zip]);

        return $dataProvider;
    }

    public function searchSmsGraph($params, $user_id): array
    {
        $query = new Query();
        $query->addSelect(['DATE(s_created_dt) as createdDate,
               SUM(IF(s_status_id= ' . SMS::STATUS_DONE . ', 1, 0)) AS smsDone,
               SUM(IF(s_status_id= ' . SMS::STATUS_ERROR . ', 1, 0)) AS smsError               
        ']);

        $query->from(static::tableName());
        $query->where('s_status_id IS NOT NULL');
        $query->andWhere(['s_created_user_id' => $user_id]);
        if($this->datetime_start && $this->datetime_end){
            $query->andWhere(['>=', 's_created_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->datetime_start))]);
            $query->andWhere(['<=', 's_created_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->datetime_end))]);
        }

        $query->groupBy('createdDate');

        return $query->createCommand()->queryAll();
    }
}
