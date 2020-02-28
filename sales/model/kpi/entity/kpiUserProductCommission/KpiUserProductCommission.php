<?php

namespace sales\model\kpi\entity\kpiUserProductCommission;

use common\models\Employee;
use common\models\query\EmployeeQuery;
use modules\product\src\entities\productType\ProductType;
use modules\product\src\entities\productType\ProductTypeQuery;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "kpi_user_product_commission".
 *
 * @property int $upc_product_type_id
 * @property int $upc_user_id
 * @property int $upc_year
 * @property int $upc_month
 * @property int|null $upc_performance
 * @property int|null $upc_commission_percent
 * @property int|null $upc_created_user_id
 * @property int|null $upc_updated_user_id
 * @property string|null $upc_created_dt
 * @property string|null $upc_updated_dt
 *
 * @property ProductType $upcProductType
 * @property Employee $upcCreatedUser
 * @property Employee $upcUpdatedUser
 * @property Employee $upcUser
 */
class KpiUserProductCommission extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'kpi_user_product_commission';
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
					ActiveRecord::EVENT_BEFORE_INSERT => ['upc_created_dt', 'upc_updated_dt'],
					ActiveRecord::EVENT_BEFORE_UPDATE => ['upc_updated_dt'],
				],
				'value' => date('Y-m-d H:i:s'),
				'preserveNonEmptyValues' => true
			],
			'user' => [
				'class' => BlameableBehavior::class,
				'createdByAttribute' => 'upc_created_user_id',
				'updatedByAttribute' => 'upc_updated_user_id',
			],
		];
	}

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['upc_product_type_id', 'upc_user_id', 'upc_year', 'upc_month'], 'required'],
            [['upc_product_type_id', 'upc_user_id', 'upc_year', 'upc_month', 'upc_performance', 'upc_commission_percent', 'upc_created_user_id', 'upc_updated_user_id'], 'integer'],
            [['upc_created_dt', 'upc_updated_dt'], 'safe'],
            [['upc_product_type_id', 'upc_user_id', 'upc_year', 'upc_month'], 'unique', 'targetAttribute' => ['upc_product_type_id', 'upc_user_id', 'upc_year', 'upc_month']],
			[['upc_commission_percent', 'upc_performance'], 'number', 'max' => 100, 'min' => 0],
			[['upc_month'], 'number', 'max' => 12, 'min' => 1],
			[['upc_year'], 'string', 'max' => 4],
			[['upc_year'], 'number', 'min' => 0],
            [['upc_product_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductType::class, 'targetAttribute' => ['upc_product_type_id' => 'pt_id']],
            [['upc_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['upc_created_user_id' => 'id']],
            [['upc_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['upc_updated_user_id' => 'id']],
            [['upc_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['upc_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'upc_product_type_id' => 'Product Type ID',
            'upc_user_id' => 'User ID',
            'upc_year' => 'Year',
            'upc_month' => 'Month',
            'upc_performance' => 'Performance',
            'upc_commission_percent' => 'Commission Percent',
            'upc_created_user_id' => 'Created User',
            'upc_updated_user_id' => 'Updated User',
            'upc_created_dt' => 'Created Dt',
            'upc_updated_dt' => 'Updated Dt',
        ];
    }

    /**
     * Gets query for [[UpcProductType]].
     *
     * @return \yii\db\ActiveQuery|ProductTypeQuery
     */
    public function getUpcProductType()
    {
        return $this->hasOne(ProductType::class, ['pt_id' => 'upc_product_type_id']);
    }

    /**
     * Gets query for [[UpcCreatedUser]].
     *
     * @return \yii\db\ActiveQuery|EmployeeQuery
     */
    public function getUpcCreatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'upc_created_user_id']);
    }

    /**
     * Gets query for [[UpcUpdatedUser]].
     *
     * @return \yii\db\ActiveQuery|EmployeeQuery
     */
    public function getUpcUpdatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'upc_updated_user_id']);
    }

    /**
     * Gets query for [[UpcUser]].
     *
     * @return \yii\db\ActiveQuery|EmployeeQuery
     */
    public function getUpcUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'upc_user_id']);
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
