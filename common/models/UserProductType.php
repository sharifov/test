<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use modules\product\src\entities\productType\ProductType;

/**
 * This is the model class for table "user-product-type".
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
    public static function tableName()
    {
        return 'user_product_type';
    }

    /**
     * @return array
     */
    public function rules()
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
    public function attributeLabels()
    {
        return [
            'upt_user_id' => 'Upt User ID',
            'upt_product_type_id' => 'Upt Product Type ID',
            'upt_commission_percent' => 'Upt Commission Percent',
            'upt_product_enabled' => 'Upt Product Enabled',
            'upt_created_user_id' => 'Upt Created User ID',
            'upt_updated_user_id' => 'Upt Updated User ID',
            'upt_created_dt' => 'Upt Created Dt',
            'upt_updated_dt' => 'Upt Updated Dt',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'upt_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductType()
    {
        return $this->hasOne(ProductType::class, ['pt_id' => 'upt_product_type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'upt_created_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedUser()
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
