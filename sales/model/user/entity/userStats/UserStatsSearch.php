<?php

namespace sales\model\user\entity\userStats;

use common\components\validators\IsArrayValidator;
use common\models\Employee;
use common\models\Lead;
use common\models\LeadFlow;
use common\models\ProfitSplit;
use common\models\UserCallStatus;
use common\models\UserOnline;
use common\models\UserParams;
use DateTime;
use sales\access\EmployeeGroupAccess;
use sales\behaviors\userModelSetting\UserModelSettingSearchBehavior;
use sales\model\clientChat\entity\ClientChat;
use sales\model\leadUserConversion\entity\LeadUserConversion;
use sales\model\user\entity\ShiftTime;
use sales\model\user\entity\userStatus\UserStatus;
use sales\model\userData\entity\UserData;
use sales\model\userData\entity\UserDataKey;
use sales\model\userModelSetting\service\UserModelSettingDictionary;
use sales\traits\UserModelSettingTrait;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class UserStatsSearch
 *
 * @property int|null $id
 * @property bool|null $uo_idle_state
 *
 * @property int|null $dateTimeType
 * @property string|null $startDt
 * @property string|null $endDt
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
            'dateTimeType' => 'Created Period',
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
            $query->addSelect([UserModelSettingDictionary::FIELD_NICKNAME => Employee::tableName() . '.nickname']);
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

        $to = date("Y-m-d 23:59:59");
        $from = date("Y-m-01 00:00:00");

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        if ($this->isFieldShow(UserModelSettingDictionary::FIELD_LEAD_CREATED)) {
            $query->addSelect([
                UserModelSettingDictionary::FIELD_LEAD_CREATED => (new Query())
                    ->select(['COUNT(' . LeadFlow::tableName() . '.lead_id)'])
                    ->from(LeadFlow::tableName())
                    ->where(LeadFlow::tableName() . '.employee_id = ' . Employee::tableName() . '.id')
                    ->andWhere(['IN', 'lf_description', [LeadFlow::DESCRIPTION_MANUAL_CREATE, LeadFlow::DESCRIPTION_CLIENT_CHAT_CREATE]])
                    ->andWhere(LeadFlow::tableName() . '.created BETWEEN :startDt AND :endDt', [
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
                    ->andWhere(['status' => Lead::STATUS_PROCESSING])
                    ->andWhere(['NOT', ['lf_from_status_id' => null]])
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
        if (
            $this->isFieldShow(UserModelSettingDictionary::FIELD_SALES_CONVERSION) ||
            $this->isFieldShow(UserModelSettingDictionary::FIELD_SALES_CONVERSION_CALL_PRIORITY) ||
            $this->isFieldShow(UserModelSettingDictionary::FIELD_LEADS_QUALIFIED_COUNT) ||
            $this->isFieldShow(UserModelSettingDictionary::FIELD_SPLIT_SHARE)
        ) {
            $query->addSelect([
                UserModelSettingDictionary::FIELD_SPLIT_SHARE => (new Query())
                    ->select(['SUM(ROUND((ps_percent / 100), 2))'])
                    ->from(Lead::tableName())
                    ->leftJoin('profit_split', 'ps_lead_id = id and ps_user_id = ' . Employee::tableName() . '.id')
                    ->where(['status' => Lead::STATUS_SOLD])
                    ->andWhere(['BETWEEN', 'DATE(l_status_dt)', $from, $to])
            ]);

            $query->addSelect([
                UserModelSettingDictionary::FIELD_LEADS_QUALIFIED_COUNT => (new Query())
                    ->select(['COUNT(luc_lead_id)'])
                    ->from(LeadUserConversion::tableName())
                    ->where('luc_user_id = ' . Employee::tableName() . '.id')
                    ->andWhere('luc_created_dt BETWEEN :startDt AND :endDt', [
                        ':startDt' => $from, ':endDt' => $to,
                    ])
            ]);

            $query->addSelect([
                UserModelSettingDictionary::FIELD_LEADS_QUALIFIED_COUNT => (new Query())
                    ->select(['COUNT(luc_lead_id)'])
                    ->from(LeadUserConversion::tableName())
                    ->where('luc_user_id = ' . Employee::tableName() . '.id')
                    ->andWhere('luc_created_dt BETWEEN :startDt AND :endDt', [
                        ':startDt' => $from, ':endDt' => $to,
                    ])
            ]);
        }

        if ($this->isFieldShow(UserModelSettingDictionary::FIELD_LEADS_SOLD_COUNT)) {
            $query->addSelect([
                UserModelSettingDictionary::FIELD_LEADS_SOLD_COUNT => (new Query())
                    ->select(['COUNT(' . Lead::tableName() . '.id)'])
                    ->from(Lead::tableName())
                    ->innerJoin(ProfitSplit::tableName(), ProfitSplit::tableName() . '.ps_lead_id = ' . Lead::tableName() . '.id')
                    ->where(ProfitSplit::tableName() . '.ps_user_id = ' . Employee::tableName() . '.id')
                    ->andWhere([Lead::tableName() . '.status' => Lead::STATUS_SOLD])
                    ->andWhere(Lead::tableName() . '.l_status_dt BETWEEN :startDt AND :endDt', [
                        ':startDt' => $from, ':endDt' => $to,
                    ])
            ]);
        }

        if ($this->isFieldShow(UserModelSettingDictionary::FIELD_SUM_GROSS_PROFIT)) {
            $query->addSelect([
                UserModelSettingDictionary::FIELD_SUM_GROSS_PROFIT => (new Query())
                    ->select([
                        'SUM(ROUND((final_profit - agents_processing_fee) * ps_percent/100, 2))'
                    ])
                    ->from(Lead::tableName())
                    ->leftJoin('profit_split', 'ps_lead_id = id and ps_user_id = ' . Employee::tableName() . '.id')
                    ->where(['status' => Lead::STATUS_SOLD])
                    ->andWhere(['BETWEEN', 'DATE(l_status_dt)', $from, $to])
            ]);
        }
        if ($this->isFieldShow(UserModelSettingDictionary::FIELD_GROSS_PROFIT_CALL_PRIORITY)) {
            $query->addSelect([
                UserModelSettingDictionary::FIELD_GROSS_PROFIT_CALL_PRIORITY => (new Query())
                    ->select([
                        'ud_value'
                    ])
                    ->from(UserData::tableName())
                    ->where(['ud_key' => UserDataKey::GROSS_PROFIT])
                    ->andWhere('ud_user_id = ' . Employee::tableName() . '.id')
                    ->andWhere(['BETWEEN', 'DATE(ud_updated_dt)', $from, $to])
            ]);
        }
        if ($this->isFieldShow(UserModelSettingDictionary::FIELD_SALES_CONVERSION_CALL_PRIORITY)) {
            $query->addSelect([
                UserModelSettingDictionary::FIELD_SALES_CONVERSION_CALL_PRIORITY => (new Query())
                    ->select([
                        'ud_value'
                    ])
                    ->from(UserData::tableName())
                    ->where(['ud_key' => UserDataKey::CONVERSION_PERCENT])
                    ->andWhere('ud_user_id = ' . Employee::tableName() . '.id')
                    ->andWhere(['BETWEEN', 'DATE(ud_updated_dt)', $from, $to])
            ]);
        }
        if ($this->isFieldShow(UserModelSettingDictionary::FIELD_CALL_PRIORITY_CURRENT)) {
            $query->addSelect([
                UserModelSettingDictionary::FIELD_CALL_PRIORITY_CURRENT => (new Query())
                    ->select([
                        'up_call_user_level'
                    ])
                    ->from(UserParams::tableName())
                    ->where('up_user_id = ' . Employee::tableName() . '.id')
                    ->andWhere(['BETWEEN', 'DATE(up_updated_dt)', $from, $to])
            ]);
        }
        if ($this->isFieldShow(UserModelSettingDictionary::FIELD_LEADS_QUALIFIED_TAKEN_COUNT)) {
            $query->addSelect([
                UserModelSettingDictionary::FIELD_LEADS_QUALIFIED_TAKEN_COUNT => (new Query())
                    ->select(['COUNT(' . LeadUserConversion::tableName() . '.luc_lead_id)'])
                    ->from(LeadUserConversion::tableName())
                    ->where('luc_user_id = ' . Employee::tableName() . '.id')
                    ->andWhere(['BETWEEN', 'luc_created_dt', $this->startDt, $this->endDt])
            ]);
        }
        if ($this->isFieldShow(UserModelSettingDictionary::FIELD_CLIENT_PHONE)) {
            $query->addSelect([
                UserModelSettingDictionary::FIELD_CLIENT_PHONE => (new Query())
                    ->select(new Expression('
                        CASE
                            WHEN us_call_phone_status = 1 AND us_is_on_call = 0 THEN 1
                            ELSE 0
                        END
                    '))
                    ->from(UserStatus::tableName())
                    ->where('us_user_id = ' . Employee::tableName() . '.id')
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
