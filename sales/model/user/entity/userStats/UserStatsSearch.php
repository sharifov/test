<?php

namespace sales\model\user\entity\userStats;

use common\components\validators\IsArrayValidator;
use common\models\Employee;
use common\models\Lead;
use common\models\LeadFlow;
use common\models\UserOnline;
use common\models\UserParams;
use DateTime;
use sales\access\EmployeeGroupAccess;
use sales\behaviors\userModelSetting\UserModelSettingSearchBehavior;
use sales\model\clientChat\entity\ClientChat;
use sales\model\user\entity\ShiftTime;
use sales\model\userModelSetting\service\UserModelSettingDictionary;
use sales\traits\UserModelSettingTrait;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class UserStatsSearch
 *
 * @property int|null $id
 * @property bool|null $uo_idle_state
 *
 * @property int|null $dateTimeType
 *
 *
 * @property string|null $startDt
 * @property int|null $endDt
 */
class UserStatsSearch extends Model
{
    use UserModelSettingTrait;

    public $id;
    public $uo_idle_state;

    public $dateTimeType = UserModelSettingDictionary::DT_TYPE_MY_CURRENT_SHIFT;
    public $startDt;
    public $endDt;

    /**
     * @param Employee $currentUser
     * @param array $defaultFields
     * @param array $config
     */
    public function __construct(Employee $currentUser, array $defaultFields = [], $config = [])
    {
        $this->currentUser = $currentUser;
        $this->fields = $defaultFields;
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            [['dateTimeType'], 'required'],
            ['dateTimeType', 'setRestrictionDateTime'],

            [['id'], 'safe'],

            [['uo_idle_state'], 'boolean', 'skipOnEmpty' => true],

            ['fields', 'filter', 'filter' => static function ($value) {
                if (empty($value)) {
                    return [];
                }
                return is_array($value) ? $value : [];
            }, 'skipOnEmpty' => false],
            ['fields', IsArrayValidator::class],
        ];
    }

    public function behaviors(): array
    {
        $behaviors = [
            'fieldsUpdate' => [
                'class' => UserModelSettingSearchBehavior::class,
                'targetClassName' => self::class
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'User',
            'uo_idle_state' => 'Status',
        ];
    }

    public function searchByUser(array $params)
    {
        $query = (new Query());
        $query->select([
            Employee::tableName() . '.id',
            'username',
            'email',
            'uo_idle_state',
            'up_work_start_tm',
            'up_work_minutes',
            'up_timezone',
        ]);
        if ($this->isFieldShow(UserModelSettingDictionary::FIELD_NICKNAME)) {
            $query->addSelect(Employee::tableName() . '.nickname');
        }

        $query->from(Employee::tableName());
        $query->leftJoin(UserOnline::tableName(), Employee::tableName() . '.id = uo_user_id');
        $query->leftJoin(UserParams::tableName(), Employee::tableName() . '.id = up_user_id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['username' => SORT_ASC],
                'attributes' => [
                    'id' => [
                        'asc' => [Employee::tableName() . '.id' => SORT_ASC],
                        'desc' => [Employee::tableName() . '.id' => SORT_DESC],
                        'label' => 'Id',
                    ],
                    'username' => [
                        'asc' => ['username' => SORT_ASC],
                        'desc' => ['username' => SORT_DESC],
                        'label' => 'Username',
                    ],
                ],
            ],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        if ($this->isFieldShow(UserModelSettingDictionary::FIELD_LEAD_CREATED)) {
            $query->addSelect([
                UserModelSettingDictionary::FIELD_LEAD_CREATED => (new Query())
                    ->select(['COUNT(' . Lead::tableName() . '.id)'])
                    ->from(Lead::tableName())
                    ->where(Lead::tableName() . '.employee_id = ' . Employee::tableName() . '.id')
                    ->andWhere(Lead::tableName() . '.created BETWEEN :startDt AND :endDt', [
                        ':startDt' => $this->startDt, ':endDt' => $this->endDt,
                    ])
            ]);
        }
        if ($this->isFieldShow(UserModelSettingDictionary::FIELD_LEAD_PROCESSING)) {
            $query->addSelect([
                UserModelSettingDictionary::FIELD_LEAD_PROCESSING => (new Query())
                    ->select(['COUNT(' . Lead::tableName() . '.id)'])
                    ->from(Lead::tableName())
                    ->where(Lead::tableName() . '.employee_id = ' . Employee::tableName() . '.id')
                    ->andWhere([Lead::tableName() . '.status' => Lead::STATUS_PROCESSING])
                    ->andWhere(Lead::tableName() . '.created BETWEEN :startDt AND :endDt', [
                        ':startDt' => $this->startDt, ':endDt' => $this->endDt,
                    ])
            ]);
        }
        if ($this->isFieldShow(UserModelSettingDictionary::FIELD_LEAD_SOLD)) {
            $query->addSelect([
                UserModelSettingDictionary::FIELD_LEAD_SOLD => (new Query())
                    ->select(['COUNT(' . Lead::tableName() . '.id)'])
                    ->from(Lead::tableName())
                    ->where(Lead::tableName() . '.employee_id = ' . Employee::tableName() . '.id')
                    ->andWhere([Lead::tableName() . '.status' => Lead::STATUS_SOLD])
                    ->andWhere(Lead::tableName() . '.created BETWEEN :startDt AND :endDt', [
                        ':startDt' => $this->startDt, ':endDt' => $this->endDt,
                    ])
            ]);
        }
        if ($this->isFieldShow(UserModelSettingDictionary::FIELD_LEAD_TRASHED)) {
            $query->addSelect([
                UserModelSettingDictionary::FIELD_LEAD_TRASHED => (new Query())
                    ->select(['COUNT(' . Lead::tableName() . '.id)'])
                    ->from(Lead::tableName())
                    ->where(Lead::tableName() . '.employee_id = ' . Employee::tableName() . '.id')
                    ->andWhere([Lead::tableName() . '.status' => Lead::STATUS_TRASH])
                    ->andWhere(Lead::tableName() . '.created BETWEEN :startDt AND :endDt', [
                        ':startDt' => $this->startDt, ':endDt' => $this->endDt,
                    ])
            ]);
        }
        if ($this->isFieldShow(UserModelSettingDictionary::FIELD_LEAD_TAKEN)) {
            $query->addSelect([
                UserModelSettingDictionary::FIELD_LEAD_TAKEN => (new Query())
                    ->select(['COUNT(' . LeadFlow::tableName() . '.lead_id)'])
                    ->from(LeadFlow::tableName())
                    ->where(LeadFlow::tableName() . '.lf_owner_id = ' . Employee::tableName() . '.id')
                    ->andWhere(LeadFlow::tableName() . '.created BETWEEN :startDt AND :endDt', [
                        ':startDt' => $this->startDt, ':endDt' => $this->endDt,
                    ])
            ]);
        }
        if ($this->isFieldShow(UserModelSettingDictionary::FIELD_CLIENT_CHAT_ACTIVE)) {
            $query->addSelect([
                UserModelSettingDictionary::FIELD_CLIENT_CHAT_ACTIVE => (new Query())
                    ->select(['COUNT(' . ClientChat::tableName() . '.cch_id)'])
                    ->from(ClientChat::tableName())
                    ->where(ClientChat::tableName() . '.cch_owner_user_id = ' . Employee::tableName() . '.id')
                    ->andWhere(['NOT IN', 'cch_status_id', ClientChat::CLOSED_STATUS_GROUP])
                    ->andWhere(ClientChat::tableName() . '.cch_created_dt BETWEEN :startDt AND :endDt', [
                        ':startDt' => $this->startDt, ':endDt' => $this->endDt,
                    ])
            ]);
        }
        if ($this->isFieldShow(UserModelSettingDictionary::FIELD_CLIENT_CHAT_IDLE)) {
            $query->addSelect([
                UserModelSettingDictionary::FIELD_CLIENT_CHAT_IDLE => (new Query())
                    ->select(['COUNT(' . ClientChat::tableName() . '.cch_id)'])
                    ->from(ClientChat::tableName())
                    ->where(ClientChat::tableName() . '.cch_owner_user_id = ' . Employee::tableName() . '.id')
                    ->andWhere([ClientChat::tableName() . '.cch_status_id' => ClientChat::STATUS_IDLE])
                    ->andWhere(ClientChat::tableName() . '.cch_created_dt BETWEEN :startDt AND :endDt', [
                        ':startDt' => $this->startDt, ':endDt' => $this->endDt,
                    ])
            ]);
        }
        if ($this->isFieldShow(UserModelSettingDictionary::FIELD_CLIENT_CHAT_PROGRESS)) {
            $query->addSelect([
                UserModelSettingDictionary::FIELD_CLIENT_CHAT_PROGRESS => (new Query())
                    ->select(['COUNT(' . ClientChat::tableName() . '.cch_id)'])
                    ->from(ClientChat::tableName())
                    ->where(ClientChat::tableName() . '.cch_owner_user_id = ' . Employee::tableName() . '.id')
                    ->andWhere([ClientChat::tableName() . '.cch_status_id' => ClientChat::STATUS_IN_PROGRESS])
                    ->andWhere(ClientChat::tableName() . '.cch_created_dt BETWEEN :startDt AND :endDt', [
                        ':startDt' => $this->startDt, ':endDt' => $this->endDt,
                    ])
            ]);
        }
        if ($this->isFieldShow(UserModelSettingDictionary::FIELD_CLIENT_CHAT_CLOSED)) {
            $query->addSelect([
                UserModelSettingDictionary::FIELD_CLIENT_CHAT_CLOSED => (new Query())
                    ->select(['COUNT(' . ClientChat::tableName() . '.cch_id)'])
                    ->from(ClientChat::tableName())
                    ->where(ClientChat::tableName() . '.cch_owner_user_id = ' . Employee::tableName() . '.id')
                    ->andWhere(['IN', 'cch_status_id', ClientChat::CLOSED_STATUS_GROUP])
                    ->andWhere(ClientChat::tableName() . '.cch_created_dt BETWEEN :startDt AND :endDt', [
                        ':startDt' => $this->startDt, ':endDt' => $this->endDt,
                    ])
            ]);
        }
        if ($this->isFieldShow(UserModelSettingDictionary::FIELD_CLIENT_CHAT_TRANSFER)) {
            $query->addSelect([
                UserModelSettingDictionary::FIELD_CLIENT_CHAT_TRANSFER => (new Query())
                    ->select(['COUNT(' . ClientChat::tableName() . '.cch_id)'])
                    ->from(ClientChat::tableName())
                    ->where(ClientChat::tableName() . '.cch_owner_user_id = ' . Employee::tableName() . '.id')
                    ->andWhere([ClientChat::tableName() . '.cch_status_id' => ClientChat::STATUS_TRANSFER])
                    ->andWhere(ClientChat::tableName() . '.cch_created_dt BETWEEN :startDt AND :endDt', [
                        ':startDt' => $this->startDt, ':endDt' => $this->endDt,
                    ])
            ]);
        }

        $query->andFilterWhere([
            Employee::tableName() . '.id' => $this->id,
        ]);

        $query->andWhere(['id' => EmployeeGroupAccess::usersIdsInCommonGroupsSubQuery($this->currentUser->getId())]);

        return $dataProvider;
    }

    public function setRestrictionDateTime()
    {
        $this->setRestrictionDateTimeByType($this->dateTimeType);
    }

    /**
     * @param int $type
     * @param string $format
     * @return string
     * @throws \Exception
     */
    private function setRestrictionDateTimeByType(int $type, string $format = 'Y-m-d H:i:s'): void
    {
        //$currentDT = (new \DateTimeImmutable('now', new \DateTimeZone($this->currentUser->getTimezone())));
        $currentDT = new DateTime('now');
        switch ($type) {
            case UserModelSettingDictionary::DT_TYPE_HOUR:
                $this->endDt = $currentDT->format($format);
                $this->startDt = $currentDT->modify('-1 hour')->format($format);
                break;
            case UserModelSettingDictionary::DT_TYPE_THREE_HOURS:
                $this->endDt = $currentDT->format($format);
                $this->startDt = $currentDT->modify('-3 hours')->format($format);
                break;
            case UserModelSettingDictionary::DT_TYPE_SIX_HOURS:
                $this->endDt = $currentDT->format($format);
                $this->startDt = $currentDT->modify('-6 hours')->format($format);
                break;
            case UserModelSettingDictionary::DT_TYPE_TWELVE_HOURS:
                $this->endDt = $currentDT->format($format);
                $this->startDt = $currentDT->modify('-12 hours')->format($format);
                break;
            case UserModelSettingDictionary::DT_TYPE_TWENTY_FOUR_HOURS:
                $this->endDt = $currentDT->format($format);
                $this->startDt = $currentDT->modify('-24 hour')->format($format);
                break;
            case UserModelSettingDictionary::DT_TYPE_TODAY:
                $this->endDt = $currentDT->format($format);
                $this->startDt = $currentDT->modify('today')->format($format);
                break;
            default:
                $shiftTime = ShiftTime::getByUser($this->currentUser);
                $this->startDt = $shiftTime->startUtcDt;
                $this->endDt = $shiftTime->endUtcDt;
        }
    }

    public function formName(): string
    {
        return '';
    }
}
