<?php

namespace sales\model\kpi\entity\kpiProductCommission;

use common\models\Employee;
use common\models\query\EmployeeQuery;
use modules\product\src\entities\productType\ProductType;
use modules\product\src\entities\productType\ProductTypeQuery;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "kpi_product_commission".
 *
 * @property int $pc_product_type_id
 * @property int $pc_performance
 * @property int $pc_commission_percent
 * @property int|null $pc_created_user_id
 * @property int|null $pc_updated_user_id
 * @property string|null $pc_created_dt
 * @property string|null $pc_updated_dt
 *
 * @property Employee $pcCreatedUser
 * @property ProductType $pcProductType
 * @property Employee $pcUpdatedUser
 */
class KpiProductCommission extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'kpi_product_commission';
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
					ActiveRecord::EVENT_BEFORE_INSERT => ['pc_created_dt', 'pc_updated_dt'],
					ActiveRecord::EVENT_BEFORE_UPDATE => ['pc_updated_dt'],
				],
				'value' => date('Y-m-d H:i:s'),
				'preserveNonEmptyValues' => true
			],
			'user' => [
				'class' => BlameableBehavior::class,
				'createdByAttribute' => 'pc_created_user_id',
				'updatedByAttribute' => 'pc_updated_user_id',
			],
		];
	}

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pc_product_type_id', 'pc_performance', 'pc_commission_percent'], 'required'],
            [['pc_product_type_id', 'pc_performance', 'pc_commission_percent', 'pc_created_user_id', 'pc_updated_user_id'], 'integer'],
            [['pc_created_dt', 'pc_updated_dt'], 'safe'],
            [['pc_commission_percent'], 'number' , 'min' => 0, 'max' => 100],
            [['pc_product_type_id', 'pc_performance', 'pc_commission_percent'], 'unique', 'targetAttribute' => ['pc_product_type_id', 'pc_performance', 'pc_commission_percent']],
            [['pc_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['pc_created_user_id' => 'id']],
            [['pc_product_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductType::class, 'targetAttribute' => ['pc_product_type_id' => 'pt_id']],
            [['pc_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['pc_updated_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pc_product_type_id' => 'Product Type ID',
            'pc_performance' => 'Performance',
            'pc_commission_percent' => 'Commission Percent',
            'pc_created_user_id' => 'Created User ID',
            'pc_updated_user_id' => 'Updated User ID',
            'pc_created_dt' => 'Created Dt',
            'pc_updated_dt' => 'Updated Dt',
        ];
    }

    /**
     * Gets query for [[PcCreatedUser]].
     *
     * @return \yii\db\ActiveQuery|EmployeeQuery
     */
    public function getPcCreatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'pc_created_user_id']);
    }

    /**
     * Gets query for [[PcProductType]].
     *
     * @return \yii\db\ActiveQuery|ProductTypeQuery
     */
    public function getPcProductType()
    {
        return $this->hasOne(ProductType::class, ['pt_id' => 'pc_product_type_id']);
    }

    /**
     * Gets query for [[PcUpdatedUser]].
     *
     * @return \yii\db\ActiveQuery|EmployeeQuery
     */
    public function getPcUpdatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'pc_updated_user_id']);
    }

    /**
     * {@inheritdoc}
     * @return Scopes the active query used by this AR class.
     */
    public static function find()
    {
        return new Scopes(get_called_class());
    }
}
