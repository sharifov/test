<?php

namespace modules\shiftSchedule\src\entities\shiftScheduleRequest;

use common\models\Employee;
use modules\shiftSchedule\src\entities\shiftScheduleRequestLog\ShiftScheduleRequestLog;
use modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;
use modules\shiftSchedule\src\services\UserShiftScheduleAttributeFormatService;
use Yii;
use yii\base\Event;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;

/**
 * This is the model class for table "shift_schedule_request".
 *
 * @property int $ssr_id
 * @property int|null $ssr_uss_id
 * @property int|null $ssr_sst_id
 * @property int $ssr_status_id
 * @property string|null $ssr_description
 * @property string|null $ssr_created_dt
 * @property string|null $ssr_updated_dt
 * @property int|null $ssr_created_user_id
 * @property int|null $ssr_updated_user_id
 *
 * @property ShiftScheduleType $srhSst
 * @property-read string $statusNameColor
 * @property-read string $statusName
 * @property-read string $scheduleTypeKey
 * @property-read string $scheduleTypeTitle
 * @property-read int $duration
 * @property UserShiftSchedule $srhUss
 * @property Employee $ssrCreatedUser
 * @property Employee $ssrUpdatedUser
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
        self::STATUS_DECLINED => 'Decline',
        self::STATUS_REMOVED => 'Remove',
    ];

    public const STATUS_LIST_COLOR = [
        self::STATUS_PENDING => 'warning',
        self::STATUS_APPROVE => 'success',
        self::STATUS_DECLINED => 'danger',
        self::STATUS_REMOVED => 'secondary',
    ];

    public const STATUS_LIST_COLOR_HEX = [
        self::STATUS_PENDING => '#f2926b',
        self::STATUS_APPROVE => '#28a745',
        self::STATUS_DECLINED => '#e15554',
        self::STATUS_REMOVED => '#6c757d',
    ];

    public const STATUS_LIST_PAST_TENSE = [
        self::STATUS_PENDING => 'pending',
        self::STATUS_APPROVE => 'approved',
        self::STATUS_DECLINED => 'declined',
        self::STATUS_REMOVED => 'removed',
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
                    BaseActiveRecord::EVENT_BEFORE_INSERT => 'ssr_created_dt',
                    BaseActiveRecord::EVENT_BEFORE_UPDATE => 'ssr_updated_dt',
                ],
                'value' => date('Y-m-d H:i:s'),
            ],
        ];
    }

    /**
     * @return void
     */
    public function init(): void
    {
        $this->on(self::EVENT_AFTER_INSERT, [$this, 'saveRequestLog']);
        $this->on(self::EVENT_AFTER_UPDATE, [$this, 'saveRequestLog']);
        parent::init();
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['ssr_uss_id', 'ssr_sst_id', 'ssr_status_id', 'ssr_created_user_id', 'ssr_updated_user_id'], 'integer'],
            [['ssr_status_id'], 'required'],
            [['ssr_created_dt', 'ssr_updated_dt'], 'safe'],
            [['ssr_description'], 'string', 'max' => 1000],
            [['ssr_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['ssr_created_user_id' => 'id']],
            [['ssr_sst_id'], 'exist', 'skipOnError' => true, 'targetClass' => ShiftScheduleType::class, 'targetAttribute' => ['ssr_sst_id' => 'sst_id']],
            [['ssr_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['ssr_updated_user_id' => 'id']],
            [['ssr_uss_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserShiftSchedule::class, 'targetAttribute' => ['ssr_uss_id' => 'uss_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'ssr_id' => 'Srh ID',
            'ssr_uss_id' => 'User Shift Schedule ID',
            'ssr_sst_id' => 'Shift Schedule Type ID',
            'ssr_status_id' => 'Status ID',
            'ssr_description' => 'Description',
            'ssr_created_dt' => 'Created Dt',
            'ssr_updated_dt' => 'Update Dt',
            'ssr_created_user_id' => 'Created User ID',
            'ssr_updated_user_id' => 'Updated User ID',
        ];
    }

    /**
     * Gets query for [[SrhSst]].
     *
     * @return ActiveQuery
     */
    public function getSrhSst(): ActiveQuery
    {
        return $this->hasOne(ShiftScheduleType::class, ['sst_id' => 'ssr_sst_id']);
    }

    /**
     * Gets query for [[SrhUss]].
     *
     * @return ActiveQuery
     */
    public function getSrhUss(): ActiveQuery
    {
        return $this->hasOne(UserShiftSchedule::class, ['uss_id' => 'ssr_uss_id']);
    }

    /**
     * @param int $ssr_uss_id
     * @param int $ssr_sst_id
     * @param int $ssr_status_id
     * @param string|null $ssr_description
     * @param int|null $ssr_created_user_id
     * @param int|null $ssr_updated_user_id
     * @return ShiftScheduleRequest
     */
    public static function create(int $ssr_uss_id, int $ssr_sst_id, int $ssr_status_id, ?string $ssr_description, ?int $ssr_created_user_id, ?int $ssr_updated_user_id): ShiftScheduleRequest
    {
        $model = new self();
        $model->ssr_uss_id = $ssr_uss_id;
        $model->ssr_sst_id = $ssr_sst_id;
        $model->ssr_status_id = $ssr_status_id;
        $model->ssr_description = $ssr_description;
        $model->ssr_created_user_id = $ssr_created_user_id;
        $model->ssr_updated_user_id = $ssr_updated_user_id;
        return $model;
    }

    /**
     * @return string
     */
    public function getStatusName(): string
    {
        return self::STATUS_LIST[$this->ssr_status_id] ?? '';
    }

    /**
     * @param int $statusId
     * @return string
     */
    public static function getStatusNameById(int $statusId): string
    {
        return self::STATUS_LIST[$statusId] ?? '';
    }

    /**
     * @param bool $hex
     * @return string
     */
    public function getStatusNameColor(bool $hex = false): string
    {
        if ($hex) {
            return self::STATUS_LIST_COLOR_HEX[$this->ssr_status_id] ?? '';
        }

        return self::STATUS_LIST_COLOR[$this->ssr_status_id] ?? '';
    }

    /**
     * @param int $statusId
     * @return string
     */
    public static function getStatusNameColorById(int $statusId): string
    {
        return self::STATUS_LIST_COLOR[$statusId] ?? '';
    }

    /**
     * @return array
     */
    public static function getStatusList(): array
    {
        return self::STATUS_LIST;
    }

    /**
     * @return string
     */
    public function getScheduleTypeKey(): string
    {
        return $this->srhSst ? $this->srhSst->sst_key : '-';
    }

    /**
     * @return string
     */
    public function getScheduleTypeTitle(): string
    {
        return (!$this->srhSst || empty($this->srhSst->sst_title)) ? '-' :
            $this->srhSst->sst_title;
    }

    /**
     * @return string
     */
    public function getDuration(): string
    {
        $duration = strtotime($this->srhUss->uss_end_utc_dt ?? 0) - strtotime($this->srhUss->uss_start_utc_dt ?? 0);
        return Yii::$app->formatter->asDuration($duration);
    }

    /**
     * Get compatible status with UserShiftSchedule statuses
     * @param int $statusId
     * @return int
     */
    public static function getCompatibleStatus(int $statusId): int
    {
        return [
                self::STATUS_PENDING => UserShiftSchedule::STATUS_PENDING,
                self::STATUS_APPROVE => UserShiftSchedule::STATUS_APPROVED,
                self::STATUS_DECLINED => UserShiftSchedule::STATUS_CANCELED,
                self::STATUS_REMOVED => UserShiftSchedule::STATUS_DELETED,
            ][$statusId] ?? UserShiftSchedule::STATUS_PENDING;
    }

    /**
     * Gets query for [[SsrCreatedUser]].
     *
     * @return ActiveQuery
     */
    public function getSsrCreatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'ssr_created_user_id']);
    }

    /**
     * Gets query for [[SsrUpdatedUser]].
     *
     * @return ActiveQuery
     */
    public function getSsrUpdatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'ssr_updated_user_id']);
    }

    /**
     * @return string
     */
    public function getStatusNamePasteTense(): string
    {
        return self::STATUS_LIST_PAST_TENSE[$this->ssr_status_id] ?? '';
    }

    public function isStatusPending(): bool
    {
        return $this->ssr_status_id === self::STATUS_PENDING;
    }

    public function isStatusDeclined(): bool
    {
        return $this->ssr_status_id === self::STATUS_DECLINED;
    }

    /**
     * Reutrn custom value of attributes
     * @return array
     */
    public function getCustomValueAttributes(): array
    {
        $attr = $this->attributes;
        $attr['ssr_status_id'] = sprintf(
            '%s (%s)',
            $this->getStatusName(),
            $this->ssr_status_id
        );
        return $attr;
    }

    /**
     * @param Event $event
     * @throws \yii\base\InvalidConfigException
     */
    public function saveRequestLog(Event $event): void
    {
        if ($event->name === BaseActiveRecord::EVENT_AFTER_INSERT) {
            $oldAttr = null;
        } else {
            $changedAttributes = $event->changedAttributes;
            if (!empty($changedAttributes['ssr_status_id'])) {
                $changedAttributes['ssr_status_id'] = sprintf(
                    '%s (%s)',
                    $event->sender::getStatusNameById($changedAttributes['ssr_status_id']),
                    $changedAttributes['ssr_status_id']
                );
                $oldAttr = json_encode($changedAttributes);
            } else {
                $oldAttr = '';
            }
        }

        $newAttr = [];
        foreach ($event->changedAttributes as $key => $attribute) {
            if (array_key_exists($key, $event->sender->customValueAttributes)) {
                $newAttr[$key] = $event->sender->customValueAttributes[$key];
            }
        }

        if (is_array($oldAttr)) {
            $oldAttr = json_encode($oldAttr);
        }
        if (is_array($newAttr)) {
            $newAttr = json_encode($newAttr);
        }

        $formatAttributeService = \Yii::createObject(UserShiftScheduleAttributeFormatService::class);
        $formattedAttr = $formatAttributeService->formatAttr(ShiftScheduleRequest::class, $oldAttr, $newAttr);

        $history = new ShiftScheduleRequestLog([
            'ssrh_ssr_id' => $event->sender->ssr_id,
            'ssrh_old_attr' => $oldAttr,
            'ssrh_new_attr' => $newAttr,
            'ssrh_formatted_attr' => $formattedAttr,
            'ssrh_updated_dt' => null,
            'ssrh_updated_user_id' => null,
        ]);

        $history->save();
    }

    /**
     * @param int $oldStatus
     * @return bool
     */
    public function isChangedStatus(int $oldStatus): bool
    {
        return $this->ssr_status_id !== $oldStatus;
    }
}
