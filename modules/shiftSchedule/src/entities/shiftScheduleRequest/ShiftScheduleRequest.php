<?php

namespace modules\shiftSchedule\src\entities\shiftScheduleRequest;

use common\models\Employee;
use modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;

/**
 * This is the model class for table "shift_schedule_request".
 *
 * @property int $srh_id
 * @property int|null $srh_uss_id
 * @property int|null $srh_sst_id
 * @property int $srh_status_id
 * @property string|null $srh_description
 * @property string|null $srh_created_dt
 * @property string|null $srh_update_dt
 * @property int|null $srh_created_user_id
 * @property int|null $srh_updated_user_id
 *
 * @property Employee $srhCreatedUser
 * @property ShiftScheduleType $srhSst
 * @property UserShiftSchedule $srhUss
 */
class ShiftScheduleRequest extends ActiveRecord
{
    public const STATUS_PENDING = 1;
    public const STATUS_APPROVE = 2;
    public const STATUS_DECLINED = 3;
    public const STATUS_REMOVED = 4;

    public const STATUS_LIST = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_APPROVE => 'Approve',
        self::STATUS_DECLINED => 'Declined',
        self::STATUS_REMOVED => 'Removed',
    ];

    public const STATUS_LIST_COLOR = [
        self::STATUS_PENDING => 'info',
        self::STATUS_APPROVE => 'success',
        self::STATUS_DECLINED => 'danger',
        self::STATUS_REMOVED => 'secondary',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'shift_schedule_request';
    }

    /**
     * @return array[]
     */
    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    BaseActiveRecord::EVENT_BEFORE_INSERT => 'srh_created_dt',
                    BaseActiveRecord::EVENT_BEFORE_UPDATE => 'srh_updated_dt',
                ],
                'value' => date('Y-m-d H:i:s'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'attributes' => [
                    BaseActiveRecord::EVENT_BEFORE_INSERT => 'srh_created_user_id',
                    BaseActiveRecord::EVENT_BEFORE_UPDATE => 'srh_updated_user_id',
                ]
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['srh_uss_id', 'srh_sst_id', 'srh_status_id', 'srh_created_user_id', 'srh_updated_user_id'], 'integer'],
            [['srh_status_id'], 'required'],
            [['srh_created_dt', 'srh_update_dt'], 'safe'],
            [['srh_description'], 'string', 'max' => 1000],
            [['srh_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['srh_created_user_id' => 'id']],
            [['srh_sst_id'], 'exist', 'skipOnError' => true, 'targetClass' => ShiftScheduleType::class, 'targetAttribute' => ['srh_sst_id' => 'sst_id']],
            [['srh_uss_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserShiftSchedule::class, 'targetAttribute' => ['srh_uss_id' => 'uss_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'srh_id' => Yii::t('app', 'Srh ID'),
            'srh_uss_id' => Yii::t('app', 'User Shift Schedule ID'),
            'srh_sst_id' => Yii::t('app', 'Shift Schedule Type ID'),
            'srh_status_id' => Yii::t('app', 'Status ID'),
            'srh_description' => Yii::t('app', 'Description'),
            'srh_created_dt' => Yii::t('app', 'Created Dt'),
            'srh_update_dt' => Yii::t('app', 'Update Dt'),
            'srh_created_user_id' => Yii::t('app', 'Created User ID'),
            'srh_updated_user_id' => Yii::t('app', 'Updated User ID'),
        ];
    }

    /**
     * Gets query for [[SrhCreatedUser]].
     *
     * @return ActiveQuery
     */
    public function getSrhCreatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'srh_created_user_id']);
    }

    /**
     * Gets query for [[SrhSst]].
     *
     * @return ActiveQuery
     */
    public function getSrhSst(): ActiveQuery
    {
        return $this->hasOne(ShiftScheduleType::class, ['sst_id' => 'srh_sst_id']);
    }

    /**
     * Gets query for [[SrhUss]].
     *
     * @return ActiveQuery
     */
    public function getSrhUss(): ActiveQuery
    {
        return $this->hasOne(UserShiftSchedule::class, ['uss_id' => 'srh_uss_id']);
    }

    /**
     * @return string
     */
    public function getStatusName(): string
    {
        return self::STATUS_LIST[$this->srh_status_id] ?? '';
    }

    /**
     * @return string
     */
    public function getStatusNameColor(): string
    {
        return self::STATUS_LIST_COLOR[$this->srh_status_id] ?? '';
    }

    /**
     * @return array
     */
    public static function getList(): array
    {
        return self::STATUS_LIST;
    }
}
