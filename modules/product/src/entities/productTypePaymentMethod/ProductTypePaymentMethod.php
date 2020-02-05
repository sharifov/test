<?php

namespace modules\product\src\entities\productTypePaymentMethod;

use common\models\Employee;
use common\models\PaymentMethod;
use modules\product\src\entities\productType\ProductType;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\validators\InlineValidator;

/**
 * This is the model class for table "product_type_payment_method".
 *
 * @property int $ptpm_produt_type_id
 * @property int $ptpm_payment_method_id
 * @property float|null $ptpm_payment_fee_percent
 * @property float|null $ptpm_payment_fee_amount
 * @property int|null $ptpm_enabled
 * @property int|null $ptpm_default
 * @property int|null $ptpm_created_user_id
 * @property int|null $ptpm_updated_user_id
 * @property string|null $ptpm_created_dt
 * @property string|null $ptpm_updated_dt
 *
 * @property Employee $ptpmCreatedUser
 * @property PaymentMethod $ptpmPaymentMethod
 * @property ProductType $ptpmProdutType
 * @property Employee $ptpmUpdatedUser
 */
class ProductTypePaymentMethod extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'product_type_payment_method';
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
					ActiveRecord::EVENT_BEFORE_INSERT => ['ptpm_created_dt', 'ptpm_updated_dt'],
					ActiveRecord::EVENT_BEFORE_UPDATE => ['ptpm_updated_dt'],
				],
				'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
			],
			'user' => [
				'class' => BlameableBehavior::class,
				'createdByAttribute' => 'ptpm_created_user_id', //'pq_owner_user_id',
				'updatedByAttribute' => 'ptpm_updated_user_id',
			],
		];
	}

    public function rules()
    {
        return [
            [['ptpm_produt_type_id', 'ptpm_payment_method_id'], 'required'],
            [['ptpm_produt_type_id', 'ptpm_payment_method_id', 'ptpm_enabled', 'ptpm_default', 'ptpm_created_user_id', 'ptpm_updated_user_id'], 'integer'],
            [['ptpm_payment_fee_amount', 'ptpm_payment_fee_percent'], 'number'],
            [['ptpm_created_dt', 'ptpm_updated_dt'], 'safe'],
            [['ptpm_payment_fee_percent'], 'filter', 'filter' => 'floatval'],
            [['ptpm_produt_type_id', 'ptpm_payment_method_id'], 'unique', 'targetAttribute' => ['ptpm_produt_type_id', 'ptpm_payment_method_id']],
            [['ptpm_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['ptpm_created_user_id' => 'id']],
            [['ptpm_payment_method_id'], 'exist', 'skipOnError' => true, 'targetClass' => PaymentMethod::class, 'targetAttribute' => ['ptpm_payment_method_id' => 'pm_id']],
            [['ptpm_produt_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductType::class, 'targetAttribute' => ['ptpm_produt_type_id' => 'pt_id']],
            [['ptpm_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['ptpm_updated_user_id' => 'id']],
            ['ptpm_default', function ($attribute, $params, InlineValidator $validator) {
        		if ($this->ptpm_default && $default = self::find()->andWhere(['ptpm_produt_type_id' => $this->ptpm_produt_type_id, 'ptpm_default' => true])->andWhere(['<>', 'ptpm_payment_method_id', $this->ptpm_payment_method_id])->one()) {
					$this->addError($attribute, 'Already exist default Product type payment method: Product - ' . $default->ptpmProdutType->pt_name . '; Payment Method: ' . $default->ptpmPaymentMethod->pm_name);
				}
			}],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ptpm_produt_type_id' => 'Produt Type',
            'ptpm_payment_method_id' => 'Payment Method',
            'ptpm_payment_fee_percent' => 'Payment Fee Percent',
            'ptpm_payment_fee_amount' => 'Payment Fee Amount',
            'ptpm_enabled' => 'Enabled',
            'ptpm_default' => 'Default',
            'ptpm_created_user_id' => 'Created User',
            'ptpm_updated_user_id' => 'Updated User',
            'ptpm_created_dt' => 'Created Dt',
            'ptpm_updated_dt' => 'Updated Dt',
        ];
    }

    /**
     * Gets query for [[PtpmCreatedUser]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPtpmCreatedUser(): \yii\db\ActiveQuery
	{
        return $this->hasOne(Employee::class, ['id' => 'ptpm_created_user_id']);
    }

    /**
     * Gets query for [[PtpmPaymentMethod]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPtpmPaymentMethod(): \yii\db\ActiveQuery
	{
        return $this->hasOne(PaymentMethod::class, ['pm_id' => 'ptpm_payment_method_id']);
    }

    /**
     * Gets query for [[PtpmProdutType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPtpmProdutType(): \yii\db\ActiveQuery
	{
        return $this->hasOne(ProductType::class, ['pt_id' => 'ptpm_produt_type_id']);
    }

    /**
     * Gets query for [[PtpmUpdatedUser]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPtpmUpdatedUser(): \yii\db\ActiveQuery
	{
        return $this->hasOne(Employee::class, ['id' => 'ptpm_updated_user_id']);
    }
}
