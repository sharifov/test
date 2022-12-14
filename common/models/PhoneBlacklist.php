<?php

namespace common\models;

use borales\extensions\phoneInput\PhoneInputValidator;
use src\entities\EventTrait;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "phone_blacklist".
 *
 * @property int $pbl_id
 * @property string $pbl_phone
 * @property string|null $pbl_description
 * @property int|null $pbl_enabled
 * @property string|null $pbl_created_dt
 * @property string|null $pbl_updated_dt
 * @property int|null $pbl_updated_user_id
 * @property string|null $pbl_expiration_date
 *
 * @property Employee $updatedUser
 */
class PhoneBlacklist extends \yii\db\ActiveRecord
{
    use EventTrait;

    public const SCENARIO_INSERT = 'insert';

    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['pbl_created_dt', 'pbl_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['pbl_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s'),
            ],
            'attribute' => [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['pbl_updated_user_id'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['pbl_updated_user_id'],
                ],
                'value' => isset(\Yii::$app->user) ? \Yii::$app->user->id : null,
            ],
        ];
    }

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'phone_blacklist';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['pbl_phone', 'required'],
            ['pbl_phone', 'string', 'max' => 30],
            ['pbl_phone', 'unique', 'on' => self::SCENARIO_DEFAULT],
//            ['pbl_phone', 'filter', 'filter' => static function($value) {
//                return $value === null ? null : str_replace(['-', ' '], '', trim($value));
//            }, 'skipOnError' => true],
            ['pbl_phone', 'match', 'pattern' => '/^\+[0-9\.\*]+$/', 'message' => 'The format of "{attribute}" is invalid. Allowed: "+", "[0-9]", ".", "*"'],
            ['pbl_enabled', 'boolean'],
            ['pbl_expiration_date', 'date', 'format' => 'php:Y-m-d H:i:s'],
            ['pbl_expiration_date', 'validateExpirationDate', 'on' => self::SCENARIO_INSERT, 'skipOnError' => true],
            ['pbl_description', 'string', 'max' => 255],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'pbl_id' => 'ID',
            'pbl_phone' => 'Phone',
            'pbl_description' => 'Description',
            'pbl_enabled' => 'Enabled',
            'pbl_created_dt' => 'Created',
            'pbl_updated_dt' => 'Updated',
            'pbl_updated_user_id' => 'Updated User',
            'pbl_expiration_date' => 'Expiration date',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getUpdatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'pbl_updated_user_id']);
    }

    /**
     * @return PhoneBlacklistQuery
     */
    public static function find(): PhoneBlacklistQuery
    {
        return new PhoneBlacklistQuery(static::class);
    }

    public static function create(string $phone, string $date, ?string $description = null): self
    {
        $self = new self();
        $self->pbl_phone = $phone;
        $self->pbl_enabled = true;
        $self->pbl_expiration_date = $date;
        $self->pbl_description = $description;
        return $self;
    }

    public function validateExpirationDate(): bool
    {
        $row = self::findOne(['pbl_phone' => $this->pbl_phone, 'pbl_enabled' => true]);

        if ($row && $row->pbl_expiration_date) {
            $expirationDate = new \DateTimeImmutable($row->pbl_expiration_date);
            $date = new \DateTimeImmutable();
            if ($date <= $expirationDate) {
                $this->addError('pbl_expiration_date', '??he expiration time has not yet expired');
                return false;
            }

            $this->addError('pbl_phone', 'This phone number already in the blacklist');
            return false;
        }

        return true;
    }
}
