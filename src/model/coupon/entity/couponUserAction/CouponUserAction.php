<?php

namespace src\model\coupon\entity\couponUserAction;

use common\models\ApiUser;
use common\models\Employee;
use src\model\coupon\entity\coupon\Coupon;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "coupon_user_action".
 *
 * @property int $cuu_id
 * @property int $cuu_coupon_id
 * @property int $cuu_action_id
 * @property int|null $cuu_api_user_id
 * @property int|null $cuu_user_id
 * @property string|null $cuu_description
 * @property string|null $cuu_created_dt
 *
 * @property ApiUser $cuuApiUser
 * @property Coupon $cuuCoupon
 * @property Employee $cuuUser
 */
class CouponUserAction extends \yii\db\ActiveRecord
{
    public const ACTION_CREATE = 1;
    public const ACTION_UPDATE = 2;
    public const ACTION_USE = 3;

    public const ACTION_LIST = [
        self::ACTION_CREATE => 'create',
        self::ACTION_UPDATE => 'update',
        self::ACTION_USE => 'use',
    ];

    public function rules(): array
    {
        return [
            ['cuu_action_id', 'required'],
            ['cuu_action_id', 'integer'],
            ['cuu_action_id', 'in', 'range' => array_keys(self::ACTION_LIST)],

            ['cuu_coupon_id', 'required'],
            ['cuu_coupon_id', 'integer'],
            ['cuu_coupon_id', 'exist', 'skipOnError' => true, 'targetClass' => Coupon::class, 'targetAttribute' => ['cuu_coupon_id' => 'c_id']],

            ['cuu_api_user_id', 'required', 'when' => function (self $model) {
                return empty($model->cuu_user_id);
            },
            'whenClient' => "function (attribute, value) {
                return $('#couponuseraction-cuu_user_id').val() == '';
            }",
            'message' => 'ApiUserId cannot be blank if UserId is empty'],
            ['cuu_api_user_id', 'integer'],
            ['cuu_api_user_id', 'exist', 'skipOnError' => true, 'targetClass' => ApiUser::class, 'targetAttribute' => ['cuu_api_user_id' => 'au_id']],

            ['cuu_user_id', 'required', 'when' => function (self $model) {
                return empty($model->cuu_api_user_id);
            },
            'whenClient' => "function (attribute, value) {
                return $('#couponuseraction-cuu_api_user_id').val() == '';
            }",
            'message' => 'UserId cannot be blank if ApiUserId is empty'],
            ['cuu_user_id', 'integer'],
            ['cuu_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['cuu_user_id' => 'id']],

            ['cuu_created_dt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],

            ['cuu_description', 'string', 'max' => 255],
        ];
    }

    public function behaviors(): array
    {
        $behaviors = [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['cuu_created_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => false,
                ],
                'value' => date('Y-m-d H:i:s')
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function getCuuApiUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(ApiUser::class, ['au_id' => 'cuu_api_user_id']);
    }

    public function getCuuCoupon(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Coupon::class, ['c_id' => 'cuu_coupon_id']);
    }

    public function getCuuUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'cuu_user_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'cuu_id' => 'ID',
            'cuu_coupon_id' => 'Coupon',
            'cuu_action_id' => 'Action',
            'cuu_api_user_id' => 'Api User',
            'cuu_user_id' => 'User',
            'cuu_description' => 'Description',
            'cuu_created_dt' => 'Created Dt',
        ];
    }

    public static function find(): CouponUserActionScopes
    {
        return new CouponUserActionScopes(static::class);
    }

    public static function tableName(): string
    {
        return 'coupon_user_action';
    }

    public static function create(
        int $couponId,
        int $actionId,
        ?int $apiUserId,
        ?int $userId,
        ?string $description = null
    ): CouponUserAction {
        $model = new self();
        $model->cuu_coupon_id = $couponId;
        $model->cuu_action_id = $actionId;
        $model->cuu_api_user_id = $apiUserId;
        $model->cuu_user_id = $userId;
        $model->cuu_description = $description;

        return $model;
    }

    public static function getActionName(?int $actionId): string
    {
        return self::ACTION_LIST[$actionId] ?? '';
    }
}
