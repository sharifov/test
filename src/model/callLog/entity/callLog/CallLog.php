<?php

namespace src\model\callLog\entity\callLog;

use common\components\validators\PhoneValidator;
use common\models\Call;
use common\models\Client;
use common\models\Conference;
use common\models\Department;
use common\models\Employee;
use common\models\Lead;
use common\models\Project;
use common\models\UserDepartment;
use src\entities\cases\Cases;
use src\model\callLog\entity\callLogCase\CallLogCase;
use src\model\callLog\entity\callLogLead\CallLogLead;
use src\model\callLog\entity\callLogQueue\CallLogQueue;
use src\model\callLog\entity\callLogRecord\CallLogRecord;
use src\model\phoneList\entity\PhoneList;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "{{%call_log}}".
 *
 * @property int $cl_id
 * @property int|null $cl_group_id
 * @property string|null $cl_call_sid
 * @property int|null $cl_type_id
 * @property int|null $cl_category_id
 * @property int|null $cl_is_transfer
 * @property int|null $cl_duration
 * @property string|null $cl_phone_from
 * @property string|null $cl_phone_to
 * @property int|null $cl_phone_list_id
 * @property int|null $cl_user_id
 * @property int|null $cl_department_id
 * @property int|null $cl_project_id
 * @property string|null $cl_call_created_dt
 * @property string|null $cl_call_finished_dt
 * @property int|null $cl_status_id
 * @property int|null $cl_client_id
 * @property float|null $cl_price
 * @property int $cl_year
 * @property int $cl_month
 * @property int|null $cl_conference_id
 * @property string|null $cl_stir_status
 *
 * @property Client $client
 * @property Department $department
 * @property CallLog $parent
 * @property CallLog[] $children
 * @property PhoneList $phoneList
 * @property Project $project
 * @property Employee $user
 * @property CallLogCase $callLogCase
 * @property Cases|null $case
 * @property CallLogLead $callLogLead
 * @property Lead|null $lead
 * @property CallLogQueue $queue
 * @property CallLogRecord $record
 * @property CallLog[] $childCalls
 * @property string $recordingUrl
 */
class CallLog extends \yii\db\ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%call_log}}';
    }

    public function rules(): array
    {
        return [
            ['cl_type_id', 'integer'],
            ['cl_type_id', 'in', 'range' => array_keys(CallLogType::getList())],

            ['cl_category_id', 'integer'],
            ['cl_category_id', 'in', 'range' => array_keys(Call::SOURCE_LIST)],

            ['cl_is_transfer', 'boolean'],

            ['cl_duration', 'integer', 'max' => 32767, 'min' => 0],

            [
                ['cl_phone_from', 'cl_phone_to'], PhoneValidator::class, 'allowClientSellerNumbers' => true, 'stringMax' => 30, 'boralesValidatorEnable' => false
            ],

            [['cl_group_id', 'cl_project_id', 'cl_client_id'], 'integer'],

            ['cl_status_id', 'integer'],
            ['cl_status_id', 'in', 'range' => array_keys(CallLogStatus::getList())],

            ['cl_user_id', 'integer'],
            ['cl_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['cl_user_id' => 'id']],

            ['cl_phone_list_id', 'integer'],
            ['cl_phone_list_id', 'exist', 'skipOnError' => true, 'targetClass' => PhoneList::class, 'targetAttribute' => ['cl_phone_list_id' => 'pl_id']],

            [['cl_call_created_dt', 'cl_call_finished_dt'], 'safe'],

            ['cl_price', 'number', 'numberPattern' => '/^([1-9][0-9]*|0)(\.[0-9]{5})?$/', 'max' => 9999],

            [['cl_call_sid'], 'string', 'max' => 34],

            [['cl_client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Client::class, 'targetAttribute' => ['cl_client_id' => 'id']],

            ['cl_department_id', 'integer'],
            ['cl_department_id', 'exist', 'skipOnError' => true, 'targetClass' => Department::class, 'targetAttribute' => ['cl_department_id' => 'dep_id']],

//            [['cl_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => self::class, 'targetAttribute' => ['cl_group_id' => 'cl_id']],

            [['cl_project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['cl_project_id' => 'id']],

            [['cl_conference_id'], 'exist', 'skipOnError' => true, 'targetClass' => Conference::class, 'targetAttribute' => ['cl_conference_id' => 'cf_id']],

            ['cl_stir_status', 'string', 'max' => 2],
            ['cl_stir_status', 'trim', 'skipOnEmpty' => true],
            ['cl_stir_status', 'filter', 'filter' => 'strtoupper', 'skipOnEmpty' => true],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'cl_id' => 'ID',
            'cl_group_id' => 'Group ID',
            'cl_call_sid' => 'Call Sid',
            'cl_type_id' => 'Type',
            'cl_category_id' => 'Category',
            'cl_is_transfer' => 'Transfer',
            'cl_duration' => 'Duration',
            'cl_phone_from' => 'Phone From',
            'cl_phone_to' => 'Phone To',
            'cl_phone_list_id' => 'Phone List',
            'phoneList.pl_phone_number' => 'Phone List',
            'cl_user_id' => 'User',
            'user.username' => 'User',
            'cl_department_id' => 'Department',
            'cl_project_id' => 'Project',
            'cl_call_created_dt' => 'Created Dt',
            'cl_call_finished_dt' => 'Finished Dt',
            'cl_status_id' => 'Status',
            'cl_client_id' => 'Client',
            'cl_price' => 'Price',
            'cl_conference_id' => 'Conference Id',
            'cl_stir_status' => 'Stir Status'
        ];
    }

    public function getClient(): ActiveQuery
    {
        return $this->hasOne(Client::class, ['id' => 'cl_client_id']);
    }

    public function getDepartment(): ActiveQuery
    {
        return $this->hasOne(Department::class, ['dep_id' => 'cl_department_id']);
    }

    public function getParent(): ActiveQuery
    {
        return $this->hasOne(static::class, ['cl_id' => 'cl_group_id']);
    }

    public function getChildren(): ActiveQuery
    {
        return $this->hasMany(static::class, ['cl_group_id' => 'cl_id']);
    }

    public function getPhoneList(): ActiveQuery
    {
        return $this->hasOne(PhoneList::class, ['pl_id' => 'cl_phone_list_id']);
    }

    public function getProject(): ActiveQuery
    {
        return $this->hasOne(Project::class, ['id' => 'cl_project_id']);
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'cl_user_id']);
    }

    public function getCase(): ActiveQuery
    {
        return $this->hasOne(Cases::class, ['cs_id' => 'clc_case_id'])->via('callLogCase');
    }

    public function getCallLogCase(): ActiveQuery
    {
        return $this->hasOne(CallLogCase::class, ['clc_cl_id' => 'cl_id']);
    }

    public function getLead(): ActiveQuery
    {
        return $this->hasOne(Lead::class, ['id' => 'cll_lead_id'])->via('callLogLead');
    }

    public function getCallLogLead(): ActiveQuery
    {
        return $this->hasOne(CallLogLead::class, ['cll_cl_id' => 'cl_id']);
    }

    public function getQueue(): ActiveQuery
    {
        return $this->hasOne(CallLogQueue::class, ['clq_cl_id' => 'cl_id']);
    }

    public function getRecord(): ActiveQuery
    {
        return $this->hasOne(CallLogRecord::class, ['clr_cl_id' => 'cl_id']);
    }

    public function getChildCalls(): array
    {
        return (new ActiveQuery($this))
            ->where(['cl_group_id' => $this->cl_id])
            ->orWhere(['cl_id' => $this->cl_id])
            ->orderBy(['cl_call_created_dt' => SORT_ASC])->all();
    }

    public function getConference(): ActiveQuery
    {
        return $this->hasOne(Conference::class, ['cf_id' => 'cl_conference_id']);
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public function isStatusCompleted(): bool
    {
        return $this->cl_status_id === CallLogStatus::COMPLETE;
    }

    public function isStatusCanceled(): bool
    {
        return $this->cl_status_id === CallLogStatus::CANCELED;
    }

    public function isStatusBusy(): bool
    {
        return $this->cl_status_id === CallLogStatus::BUSY;
    }

    public function isStatusNoAnswer(): bool
    {
        return $this->cl_status_id === CallLogStatus::NOT_ANSWERED;
    }

    public function isStatusFailed(): bool
    {
        return $this->cl_status_id === CallLogStatus::FAILED;
    }

    public function isIn(): bool
    {
        return $this->cl_type_id === CallLogType::IN;
    }

    public function isOut(): bool
    {
        return $this->cl_type_id === CallLogType::OUT;
    }

    public function isOwner(int $userId): bool
    {
        return $this->cl_user_id === $userId;
    }

    /**
     * @return string
     */
    public function getStatusIcon(): string
    {
        if ($this->isStatusCompleted()) {
            $icon = 'fa fa-flag text-success';
        } elseif ($this->isStatusCanceled() || $this->isStatusNoAnswer() || $this->isStatusBusy() || $this->isStatusFailed()) {
            $icon = 'fa fa-times-circle text-danger';
        } else {
            $icon = '';
        }

        return '<i class="' . $icon . '"></i>';
    }

    public function getRecordingUrl(): string
    {
        return \Yii::$app->communication->getCallRecordingUrl($this->cl_call_sid);
    }

    public function isClientNotification(): bool
    {
        return $this->cl_category_id === Call::SOURCE_CLIENT_NOTIFICATION;
    }

    /**
     * Checks current object for availability by received user
     *
     * Function useful for checking that user can view, update and/or delete current object
     *
     * @param Employee $user
     * @return bool
     */
    public function isAvailableForUser(Employee $user): bool
    {
        return UserDepartment::find()
            ->where(['ud_user_id' => $user->getPrimaryKey(), 'ud_dep_id' => $this->cl_department_id])
            ->exists();
    }
}
