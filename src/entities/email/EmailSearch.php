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
use src\entities\email\helpers\EmailFilterType;
use src\entities\email\helpers\EmailType;

/**
 * EmailSearch
 *
 * @property string $e_template_type_name
 */
class EmailSearch extends Email
{
    public $email_type_id;
    public $supervision_id;
    public $template_type_name;

    public $datetime_start;
    public $datetime_end;
    public $date_range;
    public const CREATE_TIME_START_DEFAULT_RANGE = '-6 days';

    public $communication_id;
    public $email_from;
    public $email_to;
    public $language_id;
    public $e_lead_id;
    public $e_case_id;

    public function rules(): array
    {
        return [
            [['datetime_start', 'datetime_end', 'communication_id', 'email_from', 'email_to', 'language_id'], 'safe'],
            [['template_type_name'], 'string'],
            [['date_range'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
            [['e_id', 'e_project_id', 'e_type_id', 'e_is_deleted', 'e_status_id', 'e_created_user_id', 'supervision_id', 'e_lead_id', 'e_case_id'], 'integer'],
        ];
    }

    public function __construct($config = [])
    {
        parent::__construct($config);
        $userTimezone = Auth::user()->userParams->up_timezone ?? 'UTC';
        $currentDate = (new \DateTimeImmutable('now', new \DateTimeZone('UTC')))->setTimezone(new \DateTimeZone($userTimezone));
        $this->date_range = ($currentDate->modify(self::CREATE_TIME_START_DEFAULT_RANGE))->format('Y-m-d') . ' 00:00:00 - ' . $currentDate->format('Y-m-d') . ' 23:59:59';
    }

    public function attributeLabels(): array
    {
        $labels = [
            'communication_id' => 'Communication ID',
        ];

        return array_merge(parent::attributeLabels(), $labels);
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = self::find()->joinWith(['contactFrom', 'contactTo', 'params']);

        $query->addSelect([
            'e_id',
            'e_project_id',
            'e_type_id',
            'e_status_id',
            'e_created_user_id',
            'e_created_dt',
        ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['e_id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);

        $dataProvider->sort->attributes['email_from'] = [
            'asc' => ['email_address.ea_email' => SORT_ASC],
            'desc' => ['email_address.ea_email' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['email_to'] = [
            'asc' => ['email_address.ea_email' => SORT_ASC],
            'desc' => ['email_address.ea_email' => SORT_DESC],
        ];

        if (!($this->load($params) && $this->validate())) {
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
            'e_project_id' => $this->e_project_id,
            'e_type_id' => $this->e_type_id,
            'e_is_deleted' => $this->e_is_deleted,
            'e_status_id' => $this->e_status_id,
            'e_created_user_id' => $this->e_created_user_id,
        ]);

        if ($this->communication_id) {
            $query->joinWith(['emailLog' => function ($q) {
                $q->andFilterWhere(['like', 'el_communication_id', $this->communication_id]);
            }]);
        }

        if ($this->e_lead_id) {
            $query->lead($this->e_lead_id);
        }

        if ($this->e_case_id) {
            $query->case($this->e_case_id);
        }

        if ($this->template_type_name) {
            $templateIds = EmailTemplateType::find()->select(['DISTINCT(etp_id) as ep_template_type_id'])->where(['like', 'etp_name', $this->template_type_name])->asArray()->all();
            if ($templateIds) {
                $query->andFilterWhere(['ep_template_type_id' => $templateIds]);
            }
        }
        if ($this->email_from) {
            $query->joinWith(['contactFrom' => function ($q) {
                $q->andFilterWhere(['like', 'ea_email', $this->email_from]);
            }]);
        }
        if ($this->email_to) {
            $query->joinWith(['contactTo' => function ($q) {
                $q->andFilterWhere(['like', 'ea_email', $this->email_to]);
            }]);
        }
        if ($this->language_id) {
            $query->joinWith(['params' => function ($q) {
                $q->andFilterWhere(['like', 'ep_language_id', $this->language_id]);
            }]);
        }

        $dataProvider->totalCount = $query->count('distinct e_id');

        return $dataProvider;
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function searchEmails($params)
    {
        $query = self::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['e_id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 12,
            ],
        ]);

        if (isset($params['email_type_id']) && $params['email_type_id'] > 0) {
            $this->email_type_id = (int) $params['email_type_id'];

            if (EmailFilterType::isAll($this->email_type_id)) {
                $query->where(['e_is_deleted' => false]);
            } elseif (EmailFilterType::isInbox($this->email_type_id)) {
                $query->where(['e_type_id' => EmailType::INBOX, 'e_is_deleted' => false]);
            } elseif (EmailFilterType::isOutbox($this->email_type_id)) {
                $query->where(['e_type_id' => EmailType::OUTBOX, 'e_is_deleted' => false]);
            } elseif (EmailFilterType::isDraft($this->email_type_id)) {
                $query->where(['e_type_id' => EmailType::DRAFT, 'e_is_deleted' => false]);
            } elseif (EmailFilterType::isTrash($this->email_type_id)) {
                $query->where(['e_is_deleted' => true]);
            }
        }

        if (isset($params['EmailSearch']['user_id']) && $params['EmailSearch']['user_id'] > 0) {
            $query->andWhere([
                'or',
                ['=', 'e_created_user_id', $params['EmailSearch']['user_id']],
            ]);
        }

        if (isset($params['EmailSearch']['email']) && !empty($params['EmailSearch']['email'])) {
            $params['EmailSearch']['email'] = strtolower(trim($params['EmailSearch']['email']));
            $query->joinWith(['contacts' => function ($q) use ($params) {
                $q->andFilterWhere(['like', 'ea_email', $params['EmailSearch']['email']]);
            }]);
        }

        if (!($this->load($params) && $this->validate())) {
            $dataProvider->setTotalCount(QueryHelper::getQueryCountInvalidModel($this, static::class . 'searchEmails' . $params['EmailSearch']['user_id'], $query, 60));
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'e_id' => $this->e_id,
            'e_project_id' => $this->e_project_id,
            'e_type_id' => $this->e_type_id,
            'e_is_deleted' => $this->e_is_deleted,
            'e_status_id' => $this->e_status_id,
            'e_created_user_id' => $this->e_created_user_id,
        ]);

        $dataProvider->setTotalCount(QueryHelper::getQueryCountValidModel($this, static::class . 'searchEmails' . $params['EmailSearch']['user_id'], $query, 60));

        return $dataProvider;
    }
}
