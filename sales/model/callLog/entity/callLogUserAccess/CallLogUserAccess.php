<?php

namespace sales\model\callLog\entity\callLogUserAccess;

use common\models\CallUserAccess;
use common\models\Employee;
use sales\model\callLog\entity\callLog\CallLog;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "{{%call_log_user_access}}".
 *
 * @property int $clua_id
 * @property int $clua_cl_id
 * @property int|null $clua_user_id
 * @property int|null $clua_access_status_id
 * @property string|null $clua_access_start_dt
 * @property string|null $clua_access_finish_dt
 */
class CallLogUserAccess extends \yii\db\ActiveRecord
{
    public static function create(int $id, ?int $userId, int $statusId, ?string $startDt, ?string $finishDt): self
    {
        $log = new self();
        $log->clua_cl_id = $id;
        $log->clua_user_id = $userId;
        $log->clua_access_status_id = $statusId;
        $log->clua_access_start_dt = $startDt;
        $log->clua_access_finish_dt = $finishDt;
        return $log;
    }

    public function rules(): array
    {
        return [
            ['clua_access_finish_dt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],

            ['clua_access_start_dt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],

            ['clua_access_status_id', 'in', 'range' => array_keys(CallUserAccess::STATUS_TYPE_LIST)],

            ['clua_cl_id', 'required'],
            ['clua_cl_id', 'integer'],
            ['clua_cl_id', 'exist', 'skipOnError' => true, 'targetClass' => CallLog::class, 'targetAttribute' => ['clua_cl_id' => 'cl_id']],

            ['clua_user_id', 'integer'],
            ['clua_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['clua_user_id' => 'id']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'clua_id' => 'ID',
            'clua_cl_id' => 'Call Log ID',
            'clua_user_id' => 'User',
            'clua_access_status_id' => 'Status ID',
            'clua_access_start_dt' => 'Start Dt',
            'clua_access_finish_dt' => 'Finish Dt',
        ];
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'clua_user_id']);
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return '{{%call_log_user_access}}';
    }
}
