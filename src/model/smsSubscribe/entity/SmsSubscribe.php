<?php

namespace src\model\smsSubscribe\entity;

use common\models\Employee;
use common\models\Project;
use common\models\Sms;
use src\model\contactPhoneList\entity\ContactPhoneList;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "sms_subscribe".
 *
 * @property int $ss_id
 * @property int|null $ss_cpl_id
 * @property int|null $ss_project_id
 * @property int $ss_status_id
 * @property string|null $ss_created_dt
 * @property string|null $ss_updated_dt
 * @property string|null $ss_deadline_dt
 * @property int|null $ss_created_user_id
 * @property int|null $ss_updated_user_id
 * @property int|null $ss_sms_id
 *
 * @property ContactPhoneList $ssCpl
 * @property Employee $ssCreatedUser
 * @property Project $ssProject
 * @property Employee $ssUpdatedUser
 */
class SmsSubscribe extends \yii\db\ActiveRecord
{
    public function rules(): array
    {
        return [
            [['ss_cpl_id', ], 'required'],
            ['ss_cpl_id', 'integer'],
            ['ss_cpl_id', 'exist', 'skipOnError' => true, 'targetClass' => ContactPhoneList::class, 'targetAttribute' => ['ss_cpl_id' => 'cpl_id']],

            ['ss_created_user_id', 'integer'],
            ['ss_created_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['ss_created_user_id' => 'id']],

            ['ss_project_id', 'integer'],
            ['ss_project_id', 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['ss_project_id' => 'id']],

            ['ss_status_id', 'integer'],
            ['ss_status_id', 'required'],
            ['ss_status_id', 'in', 'range' => array_keys(SmsSubscribeStatus::STATUS_LIST)],

            ['ss_updated_user_id', 'integer'],
            ['ss_updated_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['ss_updated_user_id' => 'id']],

            [['ss_created_dt', 'ss_updated_dt', 'ss_deadline_dt'], 'datetime', 'format' => 'php:Y-m-d H:i:s', 'skipOnEmpty' => true],

            ['ss_sms_id', 'integer'],
            ['ss_sms_id', 'exist', 'skipOnError' => true, 'targetClass' => Sms::class, 'targetAttribute' => ['ss_sms_id' => 's_id']],
        ];
    }

    public function behaviors(): array
    {
        $behaviors = [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['ss_created_dt', 'ss_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['ss_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['ss_created_user_id'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['ss_updated_user_id'],
                ],
                'defaultValue' => null
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function getSsCpl(): \yii\db\ActiveQuery
    {
        return $this->hasOne(ContactPhoneList::class, ['cpl_id' => 'ss_cpl_id']);
    }

    public function getSsCreatedUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'ss_created_user_id']);
    }

    public function getSsProject(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Project::class, ['id' => 'ss_project_id']);
    }

    public function getSsUpdatedUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'ss_updated_user_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'ss_id' => 'ID',
            'ss_cpl_id' => 'ContactPhoneList ID',
            'ss_project_id' => 'Project ID',
            'ss_status_id' => 'Status ID',
            'ss_created_dt' => 'Created Dt',
            'ss_updated_dt' => 'Updated Dt',
            'ss_deadline_dt' => 'Deadline Dt',
            'ss_created_user_id' => 'Created User',
            'ss_updated_user_id' => 'Updated User',
            'ss_sms_id' => 'SMS ID',
        ];
    }

    public static function create(
        int $cplId,
        int $projectId,
        int $statusId = SmsSubscribeStatus::STATUS_NEW
    ): SmsSubscribe {
        $model = new self();
        $model->ss_cpl_id = $cplId;
        $model->ss_project_id = $projectId;
        $model->ss_status_id = $statusId;
        return $model;
    }

    public static function find(): SmsSubscribeScopes
    {
        return new SmsSubscribeScopes(static::class);
    }

    public static function tableName(): string
    {
        return 'sms_subscribe';
    }
}
