<?php

namespace modules\shiftSchedule\src\entities\userShiftSchedule;

use common\models\Employee;
use modules\shiftSchedule\src\entities\shift\Shift;
use modules\shiftSchedule\src\entities\shiftScheduleRule\ShiftScheduleRule;
use modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
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
 * @property bool|null $uss_customized
 * @property string|null $uss_created_dt
 * @property string|null $uss_updated_dt
 * @property int|null $uss_created_user_id
 * @property int|null $uss_updated_user_id
 * @property int|null $uss_sst_id
 * @property string $uss_year_start
 * @property int $uss_month_start
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

    public const STATUS_PENDING = 1;
    public const STATUS_APPROVED = 2;
    public const STATUS_DONE = 3;
    public const STATUS_CANCELED = 6;
    public const STATUS_DELETED = 8;

    public const DEFAULT_DURATION_HOURS = 8;

    private const STATUS_LIST = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_APPROVED => 'Approved',
        self::STATUS_DONE => 'Done',
        self::STATUS_CANCELED => 'Canceled',
        self::STATUS_DELETED => 'Deleted',
    ];

    public const TYPE_AUTO = 1;
    public const TYPE_MANUAL = 2;

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

    public function beforeSave($insert): bool
    {
        if (!$this->uss_year_start) {
            $this->uss_year_start = (int) date('Y');
        }
        if (!$this->uss_month_start) {
            $this->uss_month_start = (int) date('m');
        }
        return parent::beforeSave($insert);
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

            ['uss_customized', 'boolean', 'skipOnEmpty' => true],
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

            [['uss_shift_id', 'uss_ssr_id', 'uss_duration',
                'uss_customized', 'uss_sst_id'], 'default', 'value' => null],
            [['uss_user_id', 'uss_shift_id', 'uss_ssr_id', 'uss_duration',
                'uss_status_id', 'uss_type_id', 'uss_customized', 'uss_sst_id'], 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],


            ['uss_year_start', 'integer', 'max' => 2200, 'min' => 2000],
            ['uss_month_start', 'integer', 'max' => 12, 'min' => 1],

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
            'uss_year_start' => 'Year Start',
            'uss_month_start' => 'Month Start',
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

    /**
     * @param int|null $typeId
     * @return string
     */
    public static function getStatusNameById(?int $typeId): string
    {
        return self::STATUS_LIST[$typeId] ?? '-';
    }

    public function getTypeName(): string
    {
        return self::TYPE_LIST[$this->uss_type_id] ?? 'Unknown type';
    }

    /**
     * @param int|null $typeId
     * @return string
     */
    public static function getTypeNameById(?int $typeId): string
    {
        return self::TYPE_LIST[$typeId] ?? '-';
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
        return (!$this->shiftScheduleRule || empty($this->shiftScheduleRule->ssr_title)) ? '-' :
            $this->shiftScheduleRule->ssr_title;
    }

    /**
     * @return string
     */
    public function getScheduleTypeTitle(): string
    {
        return (!$this->shiftScheduleType || empty($this->shiftScheduleType->sst_title)) ? '-' :
            $this->shiftScheduleType->sst_title;
    }

    /**
     * @return string
     */
    public function getScheduleTypeKey(): string
    {
        return $this->shiftScheduleType ? $this->shiftScheduleType->sst_key : '-';
    }

    /**
     * @return string
     */
    public function getShiftTitle(): string
    {
        return (!$this->shift || empty($this->shift->sh_title)) ? '-' : $this->shift->sh_title;
    }

    public static function create(
        int $userId,
        ?string $description,
        string $startDateTime,
        string $endDateTime,
        int $duration,
        int $status,
        int $type,
        int $scheduleType
    ): self {
        $self = new self();
        $self->uss_user_id = $userId;
        $self->uss_description = $description;
        $self->uss_start_utc_dt = $startDateTime;
        $self->uss_end_utc_dt = $endDateTime;
        $self->uss_duration = $duration;
        $self->uss_status_id = $status;
        $self->uss_type_id = $type;
        $self->uss_sst_id = $scheduleType;
        return $self;
    }

    public function isOwner(int $userId): bool
    {
        return $this->uss_user_id === $userId;
    }

    public function editFromCalendar(
        int $status,
        int $type,
        \DateTimeImmutable $startDateTime,
        \DateTimeImmutable $endDateTime,
        int $duration,
        ?string $description
    ): void {
        $this->uss_status_id = $status;
        $this->uss_sst_id = $type;
        $this->uss_start_utc_dt = $startDateTime->format('Y-m-d H:i:s');
        $this->uss_end_utc_dt = $endDateTime->format('Y-m-d H:i:s');
        $this->uss_duration = $duration;
        $this->uss_description = $description;
    }
}
