<?php

namespace sales\model\callLog\entity\callLog;

use common\components\validators\PhoneValidator;
use common\models\Client;
use common\models\Department;
use common\models\Employee;
use common\models\Lead;
use common\models\Project;
use sales\entities\cases\Cases;
use sales\model\callLog\entity\callLogCase\CallLogCase;
use sales\model\callLog\entity\callLogLead\CallLogLead;
use sales\model\callLog\entity\callLogQueue\CallLogQueue;
use sales\model\callLog\entity\callLogRecord\CallLogRecord;
use sales\model\phoneList\entity\PhoneList;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "{{%call_log}}".
 *
 * @property int $cl_id
 * @property int|null $cl_parent_id
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
            ['cl_category_id', 'in', 'range' => array_keys(CallLogCategory::getList())],

            ['cl_is_transfer', 'boolean'],

            ['cl_duration', 'integer', 'max' => 32767, 'min' => 0],

            [['cl_phone_from', 'cl_phone_to'], PhoneValidator::class],

            [['cl_parent_id', 'cl_project_id', 'cl_client_id'], 'integer'],

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

            [['cl_parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => self::class, 'targetAttribute' => ['cl_parent_id' => 'cl_id']],

            [['cl_project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['cl_project_id' => 'id']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'cl_id' => 'ID',
            'cl_parent_id' => 'Parent ID',
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
            'cl_call_created_dt' => 'Call Created Dt',
            'cl_call_finished_dt' => 'Call Finished Dt',
            'cl_status_id' => 'Status',
            'cl_client_id' => 'Client',
            'cl_price' => 'Price',
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
        return $this->hasOne(static::class, ['cl_id' => 'cl_parent_id']);
    }

    public function getChildren(): ActiveQuery
    {
        return $this->hasMany(static::class, ['cl_parent_id' => 'cl_id']);
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

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }
}
