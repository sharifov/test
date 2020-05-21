<?php

namespace sales\model\saleTicket\entity;

use common\models\CaseSale;
use common\models\Employee;
use sales\entities\cases\Cases;
use Yii;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "sale_ticket".
 *
 * @property int $st_id
 * @property int $st_case_id
 * @property int $st_case_sale_id
 * @property string|null $st_ticket_number
 * @property string|null $st_record_locator
 * @property string|null $st_client_name
 * @property string|null $st_original_fop
 * @property int|null $st_charge_system
 * @property string|null $st_penalty_type
 * @property float|null $st_penalty_amount
 * @property float|null $st_selling
 * @property float|null $st_service_fee
 * @property float|null $st_recall_commission
 * @property float|null $st_markup
 * @property float|null $st_upfront_charge
 * @property float|null $st_refundable_amount
 * @property string|null $st_created_dt
 * @property string|null $st_updated_dt
 * @property int|null $st_created_user_id
 * @property int|null $st_updated_user_id
 *
 * @property CaseSale $stCaseSale
 * @property Cases $stCase
 * @property Employee $stCreatedUser
 * @property Employee $stUpdatedUser
 */
class SaleTicket extends \yii\db\ActiveRecord
{
	private const CHARGE_SYSTEM_STRIPE = 1;
	private const CHARGE_SYSTEM_AIRLINE = 2;
	private const CHARGE_SYSTEM_CONNEXPAY = 3;

	private const CHARGE_SYSTEM_LIST = [
		self::CHARGE_SYSTEM_STRIPE => 'Stripe',
		self::CHARGE_SYSTEM_AIRLINE => 'Airline',
		self::CHARGE_SYSTEM_CONNEXPAY => 'Connexpay'
	];

	public function beforeSave($insert)
	{
		$this->st_upfront_charge = $this->calculateUpfrontCharge();
		$this->st_refundable_amount = $this->calculateRefundableAmount();
		return parent::beforeSave($insert); // TODO: Change the autogenerated stub
	}

	public function behaviors(): array
	{
		return [
			'timestamp' => [
				'class' => TimestampBehavior::class,
				'attributes' => [
					ActiveRecord::EVENT_BEFORE_INSERT => ['st_created_dt', 'st_updated_dt'],
					ActiveRecord::EVENT_BEFORE_UPDATE => ['st_updated_dt'],
				],
				'value' => date('Y-m-d H:i:s'),
			],
			'attribute' => [
				'class' => AttributeBehavior::class,
				'attributes' => [
					ActiveRecord::EVENT_BEFORE_INSERT => ['st_created_user_id', 'st_updated_user_id'],
					ActiveRecord::EVENT_BEFORE_UPDATE => ['st_updated_user_id'],
				],
				'value' => \Yii::$app->user->id ?? null,
			],
		];
	}

	public function rules(): array
    {
        return [
            ['st_id', 'integer'],
            [['st_case_id', 'st_case_sale_id'], 'exist', 'skipOnError' => true, 'targetClass' => CaseSale::class, 'targetAttribute' => ['st_case_id' => 'css_cs_id', 'st_case_sale_id' => 'css_sale_id']],

            ['st_case_id', 'required'],
            ['st_case_id', 'integer'],

            ['st_case_sale_id', 'required'],
            ['st_case_sale_id', 'integer'],

            ['st_charge_system', 'integer'],

            ['st_created_dt', 'safe'],

            ['st_created_user_id', 'integer'],
            ['st_created_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['st_created_user_id' => 'id']],

            ['st_markup', 'number', 'max' => 999719.99],

            ['st_client_name', 'string', 'max' => 50],

            ['st_original_fop', 'string', 'max' => 5],

            ['st_penalty_amount', 'number'],

            ['st_penalty_type', 'string', 'max' => 30],

            ['st_recall_commission', 'number'],

            ['st_record_locator', 'string', 'max' => 8],

            ['st_refundable_amount', 'number'],

            ['st_selling', 'number'],

            ['st_service_fee', 'number'],

            ['st_ticket_number', 'string', 'max' => 15],

            ['st_updated_dt', 'safe'],

            ['st_updated_user_id', 'integer'],
            ['st_updated_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['st_updated_user_id' => 'id']],

            ['st_upfront_charge', 'number']
        ];
    }

    public function getStCaseSale(): \yii\db\ActiveQuery
    {
        return $this->hasOne(CaseSale::class, ['css_cs_id' => 'st_case_id', 'css_sale_id' => 'st_case_sale_id']);
    }

    public function getStCase(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Cases::class, ['cs_id' => 'st_case_id']);
    }

    public function getStCreatedUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'st_created_user_id']);
    }

    public function getStUpdatedUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'st_updated_user_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'st_id' => 'ID',
            'st_case_id' => 'Case ID',
            'st_case_sale_id' => 'Case Sale ID',
            'st_ticket_number' => 'Ticket Number',
            'st_client_name' => 'Client Name',
            'st_record_locator' => 'Record Locator',
            'st_original_fop' => 'Original Fop',
            'st_charge_system' => 'Charge System',
            'st_penalty_type' => 'Penalty Type',
            'st_penalty_amount' => 'Penalty Amount',
            'st_selling' => 'Selling',
            'st_service_fee' => 'Service Fee',
            'st_recall_commission' => 'Recall Commission',
            'st_markup' => 'Markup',
            'st_upfront_charge' => 'Upfront Charge',
            'st_refundable_amount' => 'Refundable Amount',
            'st_created_dt' => 'Created Dt',
            'st_updated_dt' => 'Updated Dt',
            'st_created_user_id' => 'Created User ID',
            'st_updated_user_id' => 'Updated User ID',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return 'sale_ticket';
    }

    public static function getChargeTypeList(): array
	{
		return self::CHARGE_SYSTEM_LIST;
	}

	public static function getChargeTypeName(int $chargeType): string
	{
		return self::getChargeTypeList()[$chargeType] ?? '';
	}

	private function calculateUpfrontCharge()
	{
		if ($this->st_original_fop === 'CC') {
			return $this->st_recall_commission + $this->st_markup + $this->st_penalty_amount - $this->st_service_fee;
		}
		return 0;
	}

	private function calculateRefundableAmount()
	{
		if (in_array($this->st_original_fop, ['CK', 'VCC'])) {
			return $this->st_selling - $this->st_recall_commission - $this->st_markup - $this->st_penalty_amount;
		}
		return 0;
	}
}
