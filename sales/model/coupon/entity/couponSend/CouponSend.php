<?php

namespace sales\model\coupon\entity\couponSend;

use common\models\Employee;
use sales\model\coupon\entity\coupon\Coupon;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "coupon_send".
 *
 * @property int $cus_id
 * @property int $cus_coupon_id
 * @property int|null $cus_user_id
 * @property int $cus_type_id
 * @property string $cus_send_to
 * @property string|null $cus_created_dt
 *
 * @property Coupon $cusCoupon
 * @property Employee $cusUser
 */
class CouponSend extends \yii\db\ActiveRecord
{
    public const TYPE_EMAIL = 1;

    public const TYPE_LIST = [
        self::TYPE_EMAIL => 'email',
    ];

    public function rules(): array
    {
        return [
            ['cus_coupon_id', 'required'],
            ['cus_coupon_id', 'integer'],
            ['cus_coupon_id', 'exist', 'skipOnError' => true, 'targetClass' => Coupon::class, 'targetAttribute' => ['cus_coupon_id' => 'c_id']],

            ['cus_created_dt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],

            ['cus_send_to', 'required'],
            ['cus_send_to', 'string', 'max' => 50],

            ['cus_type_id', 'required'],
            ['cus_type_id', 'integer'],
            ['cus_type_id', 'in', 'range' => array_keys(self::TYPE_LIST)],

            ['cus_user_id', 'required'],
            ['cus_user_id', 'integer'],
            ['cus_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['cus_user_id' => 'id']],
        ];
    }

    public function behaviors(): array
    {
        $behaviors = [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['cus_created_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => false,
                ],
                'value' => date('Y-m-d H:i:s')
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function getCusCoupon(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Coupon::class, ['c_id' => 'cus_coupon_id']);
    }

    public function getCusUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'cus_user_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'cus_id' => 'Id',
            'cus_coupon_id' => 'Coupon ID',
            'cus_user_id' => 'User ID',
            'cus_type_id' => 'Type',
            'cus_send_to' => 'Send To',
            'cus_created_dt' => 'Created Dt',
        ];
    }

    public static function find(): CouponSendScopes
    {
        return new CouponSendScopes(static::class);
    }

    public static function tableName(): string
    {
        return 'coupon_send';
    }

    public static function getTypeName(?int $typeId): string
    {
        return self::TYPE_LIST[$typeId] ?? '';
    }

    public static function create(
        int $couponId,
        int $userId,
        string $sendTo,
        int $typeId = self::TYPE_EMAIL
    ): CouponSend {
        $model = new self();
        $model->cus_coupon_id = $couponId;
        $model->cus_user_id = $userId;
        $model->cus_type_id = $typeId;
        $model->cus_send_to = $sendTo;
        return $model;
    }
}
