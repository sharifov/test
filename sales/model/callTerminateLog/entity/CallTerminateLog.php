<?php

namespace sales\model\callTerminateLog\entity;

use common\models\Project;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "call_terminate_log".
 *
 * @property int $ctl_id
 * @property string $ctl_call_phone_number
 * @property int $ctl_call_status_id
 * @property int|null $ctl_project_id
 * @property string|null $ctl_created_dt
 *
 * @property Project $ctlProject
 */
class CallTerminateLog extends \yii\db\ActiveRecord
{
    public function rules(): array
    {
        return [
            ['ctl_call_phone_number', 'required'],
            ['ctl_call_phone_number', 'string', 'max' => 100],

            ['ctl_call_status_id', 'required'],
            ['ctl_call_status_id', 'integer'],

            ['ctl_created_dt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],

            ['ctl_project_id', 'integer'],
            ['ctl_project_id', 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['ctl_project_id' => 'id']],
        ];
    }

    public function behaviors(): array
    {
        $behaviors = [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['ctl_created_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => false,
                ],
                'value' => date('Y-m-d H:i:s')
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function getCtlProject(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Project::class, ['id' => 'ctl_project_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'ctl_id' => 'ID',
            'ctl_call_phone_number' => 'Call Phone Number',
            'ctl_call_status_id' => 'Call Status ID',
            'ctl_project_id' => 'Project ID',
            'ctl_created_dt' => 'Created',
        ];
    }

    public static function find(): CallTerminateLogScopes
    {
        return new CallTerminateLogScopes(static::class);
    }

    public static function tableName(): string
    {
        return 'call_terminate_log';
    }

    public static function create($phoneNumber, $statusId, $projectId = null): CallTerminateLog
    {
        $model = new self();
        $model->ctl_call_phone_number = $phoneNumber;
        $model->ctl_call_status_id = $statusId;
        $model->ctl_project_id = $projectId;

        return $model;
    }
}
