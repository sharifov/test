<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use modules\product\src\entities\productType\ProductType;

/**
 *
 * @property int $upt_user_id
 * @property int $upt_product_type_id
 * @property float|null $upt_commission_percent
 * @property int|null $upt_product_enabled
 * @property int|null $upt_created_user_id
 * @property int|null $upt_updated_user_id
 * @property string|null $upt_created_dt
 * @property string|null $upt_updated_dt
 *
 * @property \yii\db\ActiveQuery $createdUser
 * @property \yii\db\ActiveQuery $productType
 * @property \yii\db\ActiveQuery $user
 * @property \yii\db\ActiveQuery $updatedUser
 */
class UserProductType extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'user_product_type';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['upt_user_id', 'upt_product_type_id'], 'required'],
            [['upt_user_id', 'upt_product_type_id', 'upt_product_enabled', 'upt_created_user_id', 'upt_updated_user_id'], 'integer'],
            [['upt_commission_percent'], 'number'],
            [['upt_created_dt', 'upt_updated_dt'], 'safe'],
            [['upt_user_id', 'upt_product_type_id'], 'unique', 'targetAttribute' => ['upt_user_id', 'upt_product_type_id']],
            [['upt_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['upt_created_user_id' => 'id']],
            [['upt_product_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductType::class, 'targetAttribute' => ['upt_product_type_id' => 'pt_id']],
            [['upt_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['upt_updated_user_id' => 'id']],
            [['upt_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['upt_user_id' => 'id']],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'upt_user_id' => 'User',
            'upt_product_type_id' => 'Product Type',
            'upt_commission_percent' => 'Commission Percent',
            'upt_product_enabled' => 'Product Enabled',
            'upt_created_user_id' => 'Created User',
            'upt_updated_user_id' => 'Updated User',
            'upt_created_dt' => 'Created',
            'upt_updated_dt' => 'Updated',
        ];
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['upt_created_dt', 'upt_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['upt_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'upt_created_user_id',
                'updatedByAttribute' => 'upt_updated_user_id',
            ],
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'upt_user_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getProductType(): ActiveQuery
    {
        return $this->hasOne(ProductType::class, ['pt_id' => 'upt_product_type_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCreatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'upt_created_user_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUpdatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'upt_updated_user_id']);
    }

    /**
     * @return query\UserProductTypeQuery|\yii\db\ActiveQuery
     */
    public static function find()
    {
        return new \common\models\query\UserProductTypeQuery(get_called_class());
    }
}
