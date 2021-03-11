<?php

namespace modules\product\src\entities\productHolder;

use modules\product\src\entities\product\Product;
use common\components\validators\PhoneValidator;
use modules\product\src\entities\productHolder\serializer\ProductHolderSerializer;
use sales\entities\serializer\Serializable;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "product_holder".
 *
 * @property int $ph_id
 * @property int|null $ph_product_id
 * @property string|null $ph_first_name
 * @property string|null $ph_last_name
 * @property string|null $ph_email
 * @property string|null $ph_phone_number
 * @property string|null $ph_created_dt
 *
 * @property Product $phProduct
 */
class ProductHolder extends \yii\db\ActiveRecord implements Serializable
{
    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['ph_created_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
        ];
    }

    public function rules(): array
    {
        return [
            [['ph_product_id', 'ph_first_name', 'ph_last_name', 'ph_email', 'ph_phone_number'], 'required'],
            ['ph_created_dt', 'safe'],

            ['ph_email', 'string', 'max' => 100],
            ['ph_email', 'email'],

            ['ph_first_name', 'string', 'max' => 50],

            ['ph_last_name', 'string', 'max' => 50],

            ['ph_phone_number', 'string', 'max' => 20],
            ['ph_phone_number', PhoneValidator::class, 'skipOnEmpty' => true],

            ['ph_product_id', 'integer'],
            ['ph_product_id', 'exist', 'skipOnError' => true, 'targetClass' => Product::class, 'targetAttribute' => ['ph_product_id' => 'pr_id']],
        ];
    }

    public function getPhProduct(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Product::class, ['pr_id' => 'ph_product_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'ph_id' => 'ID',
            'ph_product_id' => 'Product ID',
            'ph_first_name' => 'First Name',
            'ph_last_name' => 'Last Name',
            'ph_email' => 'Email',
            'ph_phone_number' => 'Phone Number',
            'ph_created_dt' => 'Created Dt',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return 'product_holder';
    }

    public static function create(
        int $productId,
        string $firstName,
        string $lastName,
        string $email,
        string $phone
    ): self {
        $holder = new self();
        $holder->ph_product_id = $productId;
        $holder->ph_first_name = $firstName;
        $holder->ph_last_name = $lastName;
        $holder->ph_email = $email;
        $holder->ph_phone_number = $phone;
        return $holder;
    }

    public function serialize(): array
    {
        return (new ProductHolderSerializer($this))->getData();
    }
}
