<?php

namespace modules\shiftSchedule\src\entities\userShiftScheduleLog;

use common\models\Employee;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;

/**
 * This is the model class for table "user_shift_schedule_log".
 *
 * @property int|null $ussl_id
 * @property int|null $ussl_uss_id
 * @property int|null $ussl_action_type
 * @property string|null $ussl_old_attr
 * @property string|null $ussl_new_attr
 * @property string|null $ussl_formatted_attr
 * @property int|null $ussl_created_user_id
 * @property string|null $ussl_created_dt
 * @property int|null $ussl_month_start
 * @property int|null $ussl_year_start
 * @property-read UserShiftSchedule $userShiftSchedule
 * @property-read Employee $userCreated
 */
class UserShiftScheduleLog extends \yii\db\ActiveRecord
{
    public const ACTION_TYPE_INSERT = 1;
    public const ACTION_TYPE_UPDATE = 2;

    public const ACTION_TYPE_LIST = [
        self::ACTION_TYPE_INSERT => 'Create',
        self::ACTION_TYPE_UPDATE => 'Update'
    ];

    public const ACTION_TYPE_AR = [
        ActiveRecord::EVENT_AFTER_INSERT => self::ACTION_TYPE_INSERT,
        ActiveRecord::EVENT_AFTER_UPDATE => self::ACTION_TYPE_UPDATE
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'user_shift_schedule_log';
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    BaseActiveRecord::EVENT_BEFORE_INSERT => ['ussl_created_dt'],
                ],
                'value' => date('Y-m-d H:i:s'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'attributes' => [
                    BaseActiveRecord::EVENT_BEFORE_INSERT => ['ussl_created_user_id'],
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
            [['ussl_uss_id'], 'required'],
            [['ussl_uss_id', 'ussl_created_user_id', 'ussl_month_start', 'ussl_year_start', 'ussl_action_type'], 'integer'],
            ['ussl_year_start', 'compare', 'compareValue' => date('Y'), 'operator' => '>='],
            ['ussl_month_start', 'integer', 'min' => '1', 'max' => '12'],
            [['ussl_old_attr', 'ussl_new_attr', 'ussl_formatted_attr', 'ussl_created_dt'], 'safe'],
            [['ussl_action_type'], 'in', 'range' => array_keys(self::ACTION_TYPE_LIST)],

            [['ussl_created_user_id'], 'exist', 'skipOnEmpty' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['ussl_created_user_id' => 'id']],
            [['ussl_uss_id'], 'exist', 'skipOnEmpty' => true, 'targetClass' => UserShiftSchedule::class, 'targetAttribute' => ['ussl_uss_id' => 'uss_id']]
        ];
    }

    public function beforeSave($insert): bool
    {
        $this->ussl_action_type = $insert ? self::ACTION_TYPE_INSERT : self::ACTION_TYPE_UPDATE;
        if (!$this->ussl_year_start) {
            $this->ussl_year_start = (int) date('Y');
        }
        if (!$this->ussl_month_start) {
            $this->ussl_month_start = (int) date('m');
        }
        return parent::beforeSave($insert);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'ussl_id' => 'ID',
            'ussl_uss_id' => 'Schedule Event',
            'ussl_old_attr' => 'Old Attr',
            'ussl_new_attr' => 'New Attr',
            'ussl_formatted_attr' => 'Formatted Attr',
            'ussl_created_user_id' => 'Created User',
            'ussl_created_dt' => 'Created Dt',
            'ussl_month_start' => 'Month Start',
            'ussl_year_start' => 'Year Start',
            'ussl_action_type' => 'Action Type',
        ];
    }

    /**
     * {@inheritdoc}
     * @return Scopes the active query used by this AR class.
     */
    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    /**
     * @return ActiveQuery
     */
    public function getUserShiftSchedule(): ActiveQuery
    {
        return $this->hasOne(UserShiftSchedule::class, ['uss_id' => 'ussl_uss_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUserCreated(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'ussl_created_user_id']);
    }

    public static function getTypeIdByAr(string $arEvent): ?string
    {
        return self::ACTION_TYPE_AR[$arEvent] ?? null;
    }

    /**
     * @return string
     */
    public function getActionTypeName(): string
    {
        return self::ACTION_TYPE_LIST[$this->ussl_action_type] ?? '';
    }
}
