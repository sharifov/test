<?php

namespace src\model\shiftSchedule\entity\userShiftSchedule;

use common\models\Employee;
use modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType;
use src\model\shiftSchedule\entity\shift\Shift;
use src\model\shiftSchedule\entity\shiftScheduleRule\ShiftScheduleRule;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;

/**
 * This is the model class for table "user_shift_schedule".
 *
 * @property int $uss_id
 * @property int $uss_user_id
 * @property int $uss_shift_id
 * @property int|null $uss_ssr_id
 * @property string|null $uss_description
 * @property string $uss_start_utc_dt
 * @property string|null $uss_end_utc_dt
 * @property int|null $uss_duration
 * @property int $uss_status_id
 * @property int $uss_type_id
 * @property int|null $uss_customized
 * @property string|null $uss_created_dt
 * @property string|null $uss_updated_dt
 * @property int|null $uss_created_user_id
 * @property int|null $uss_updated_user_id
 * @property int|null $uss_sst_id
 *
 * @property string $statusName
 * @property string $typeName
 *
 * @property Shift $shift
 * @property ShiftScheduleRule $shiftScheduleRule
 * @property ShiftScheduleType $shiftScheduleType
 * @property-read ActiveQuery $createdUser
 * @property-read ActiveQuery $updatedUser
 * @property Employee $user
 */
class UserShiftSchedule extends \yii\db\ActiveRecord
{
    private const MAX_VALUE_INT = 2147483647;

    private const STATUS_PENDING = 1;
    private const STATUS_APPROVED = 2;
    private const STATUS_DONE = 3;
    private const STATUS_CANCELED = 6;
    private const STATUS_DELETED = 8;

    private const STATUS_LIST = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_APPROVED => 'Approved',
        self::STATUS_DONE => 'Done',
        self::STATUS_CANCELED => 'Canceled',
        self::STATUS_DELETED => 'Deleted',
    ];

    private const TYPE_AUTO = 1;
    private const TYPE_MANUAL = 2;

    private const TYPE_LIST = [
        self::TYPE_AUTO => 'Auto',
        self::TYPE_MANUAL => 'Manual'
    ];

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    BaseActiveRecord::EVENT_BEFORE_INSERT => ['uss_created_dt', 'uss_updated_dt'],
                    BaseActiveRecord::EVENT_BEFORE_UPDATE => ['uss_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'attributes' => [
                    BaseActiveRecord::EVENT_BEFORE_INSERT => ['uss_created_user_id'],
                    BaseActiveRecord::EVENT_BEFORE_UPDATE => ['uss_updated_user_id'],
                ]
            ],
        ];
    }

    public function rules(): array
    {
        return [
            ['uss_user_id', 'required'],
            ['uss_user_id', 'integer', 'max' => self::MAX_VALUE_INT],
            ['uss_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class,
                'targetAttribute' => ['uss_user_id' => 'id']],

//            ['uss_shift_id', 'required'],
            ['uss_shift_id', 'integer', 'max' => self::MAX_VALUE_INT],
            ['uss_shift_id', 'exist', 'skipOnError' => true, 'targetClass' => Shift::class,
                'targetAttribute' => ['uss_shift_id' => 'sh_id']],

            ['uss_ssr_id', 'integer', 'max' => self::MAX_VALUE_INT],
            ['uss_ssr_id', 'exist', 'skipOnError' => true, 'targetClass' => ShiftScheduleRule::class,
                'targetAttribute' => ['uss_ssr_id' => 'ssr_id']],

            ['uss_description', 'string', 'max' => 500],

            ['uss_start_utc_dt', 'required'],
            ['uss_start_utc_dt', 'safe'],
            ['uss_end_utc_dt', 'safe'],

            ['uss_customized', 'integer', 'max' => 1, 'min' => 0, 'skipOnEmpty' => true],
            ['uss_duration', 'integer', 'max' => self::MAX_VALUE_INT],

            ['uss_status_id', 'required'],
            ['uss_status_id', 'integer', 'max' => 9, 'min' => 0],

            ['uss_type_id', 'required'],
            ['uss_type_id', 'integer', 'max' => 9, 'min' => 0],

            ['uss_created_dt', 'safe'],
            //['uss_created_dt', 'date', 'format' => 'php:Y-m-d'],
            ['uss_updated_dt', 'safe'],
            //['uss_created_dt', 'date', 'format' => 'php:Y-m-d'],

            ['uss_created_user_id', 'integer'],
            ['uss_updated_user_id', 'integer'],
            ['uss_sst_id', 'integer'],

            [['uss_sst_id'], 'exist', 'skipOnError' => true, 'targetClass' => ShiftScheduleType::class,
                'targetAttribute' => ['uss_sst_id' => 'sst_id']],
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getShift(): ActiveQuery
    {
        return $this->hasOne(Shift::class, ['sh_id' => 'uss_shift_id']);
    }

    public function getShiftScheduleRule(): ActiveQuery
    {
        return $this->hasOne(ShiftScheduleRule::class, ['ssr_id' => 'uss_ssr_id']);
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'uss_user_id']);
    }

    public function getCreatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'uss_created_user_id']);
    }

    public function getUpdatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'uss_updated_user_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getShiftScheduleType(): ActiveQuery
    {
        return $this->hasOne(ShiftScheduleType::class, ['sst_id' => 'uss_sst_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'uss_id' => 'ID',
            'uss_user_id' => 'User',
            'uss_shift_id' => 'Shift',
            'uss_ssr_id' => 'Schedule Rule',
            'uss_description' => 'Description',
            'uss_start_utc_dt' => 'Start DateTime (UTC)',
            'uss_end_utc_dt' => 'End DateTime (UTC)',
            'uss_duration' => 'Duration',
            'uss_status_id' => 'Status',
            'uss_type_id' => 'Type',
            'uss_customized' => 'Customized',
            'uss_created_dt' => 'Created Dt',
            'uss_updated_dt' => 'Updated Dt',
            'uss_created_user_id' => 'Created User',
            'uss_updated_user_id' => 'Updated User',
            'uss_sst_id' => 'Schedule Type',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return 'user_shift_schedule';
    }

    public function getStatusName(): string
    {
        return self::STATUS_LIST[$this->uss_status_id] ?? 'Unknown status';
    }

    public function getTypeName(): string
    {
        return self::TYPE_LIST[$this->uss_type_id] ?? 'Unknown type';
    }

    public static function getStatusList(): array
    {
        return self::STATUS_LIST;
    }

    public static function getTypeList(): array
    {
        return self::TYPE_LIST;
    }

    /**
     * @return string
     */
    public function getRuleTitle(): string
    {
        return $this->shiftScheduleRule ? $this->shiftScheduleRule->ssr_title : '-';
    }

    /**
     * @return string
     */
    public function getScheduleTypeTitle(): string
    {
        return $this->shiftScheduleType ? $this->shiftScheduleType->sst_title : '-';
    }

    /**
     * @return string
     */
    public function getShiftTitle(): string
    {
        return $this->shift ? $this->shift->sh_title : '-';
    }


}
