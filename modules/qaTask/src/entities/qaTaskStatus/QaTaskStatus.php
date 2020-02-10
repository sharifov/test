<?php

namespace modules\qaTask\src\entities\qaTaskStatus;

use common\models\Employee;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\bootstrap4\Html;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%qa_task_status}}".
 *
 * @property int $ts_id
 * @property string $ts_name
 * @property string|null $ts_description
 * @property int $ts_enabled
 * @property string|null $ts_css_class
 * @property int|null $ts_created_user_id
 * @property int|null $ts_updated_user_id
 * @property string $ts_created_dt
 * @property string|null $ts_updated_dt
 *
 * @property Employee $createdUser
 * @property Employee $updatedUser
 */
class QaTaskStatus extends \yii\db\ActiveRecord
{
    public const PENDING = 1;
    public const PROCESSING = 2;
    public const ESCALATED = 3;
    public const CLOSED = 4;
    public const CANCELED = 5;

    private const LIST = [
        self::PENDING => 'Pending',
        self::PROCESSING => 'Processing',
        self::ESCALATED => 'Escalated',
        self::CLOSED => 'Closed',
        self::CANCELED => 'Canceled',
    ];

    private const CSS_CLASS_LIST = [
        self::PENDING => 'info',
        self::PROCESSING => 'success',
        self::ESCALATED => 'primary',
        self::CLOSED => 'warning',
        self::CANCELED => 'danger',
    ];

    public static function getName(?int $value)
    {
        return self::LIST[$value] ?? 'Undefined';
    }

    public static function getCssClass(?int $value): string
    {
        return self::CSS_CLASS_LIST[$value] ?? 'secondary';
    }

    public static function asFormat(?int $value): string
    {
        return Html::tag(
            'span',
            self::getName($value),
            ['class' => 'badge badge-' . self::getCssClass($value)]
        );
    }

    public static function getList(): array
    {
        return self::LIST;
    }

    public static function guard(?int $startStatus, int $endStatus): void
    {
        self::guardTransferFrom($startStatus);
        self::guardTransferTo($startStatus, $endStatus);
    }

    private static function guardTransferFrom(?int $startStatus): void
    {
        //todo
    }

    private static function guardTransferTo(?int $startStatus, int $endStatus): void
    {
        //todo
    }

    public static function tableName(): string
    {
        return '{{%qa_task_status}}';
    }

    public function rules(): array
    {
        return [
            ['ts_id', 'required'],
            ['ts_id', 'integer'],
            ['ts_id', 'unique'],

            ['ts_name', 'required'],
            ['ts_name', 'string', 'max' => 30],

            ['ts_description', 'string', 'max' => 255],

            ['ts_enabled', 'required'],
            ['ts_enabled', 'boolean'],

            ['ts_css_class', 'string', 'max' => 100],
        ];
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['ts_created_dt', 'ts_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['ts_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'ts_created_user_id',
                'updatedByAttribute' => 'ts_updated_user_id',
            ],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'ts_id' => 'ID',
            'ts_name' => 'Name',
            'ts_description' => 'Description',
            'ts_enabled' => 'Enabled',
            'ts_css_class' => 'Css Class',
            'ts_created_user_id' => 'Created User',
            'createdUser' => 'Created User',
            'ts_updated_user_id' => 'Updated User',
            'updatedUser' => 'Updated User',
            'ts_created_dt' => 'Created Dt',
            'ts_updated_dt' => 'Updated Dt',
        ];
    }

    public function getCreatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'ts_created_user_id']);
    }

    public function getUpdatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'ts_updated_user_id']);
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }
}
