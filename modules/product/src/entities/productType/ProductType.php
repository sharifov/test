<?php

namespace modules\product\src\entities\productType;

use sales\entities\EventTrait;

/**
 * This is the model class for table "product_type".
 *
 * @property int $pt_id
 * @property string $pt_key
 * @property string $pt_name
 * @property string $pt_description
 * @property double $pt_service_fee_percent
 * @property array $pt_settings
 * @property bool $pt_enabled
 * @property string $pt_created_dt
 * @property string $pt_updated_dt
 */
class ProductType extends \yii\db\ActiveRecord
{
    use EventTrait;

    public const PRODUCT_FLIGHT = 1;
    public const PRODUCT_HOTEL  = 2;

    public static function tableName(): string
    {
        return 'product_type';
    }

    public function rules(): array
    {
        return [
            [['pt_id', 'pt_key', 'pt_name'], 'required'],
            [['pt_id'], 'integer'],
            [['pt_service_fee_percent'], 'number'],
            [['pt_enabled'], 'boolean'],
            [['pt_description'], 'string'],
            [['pt_settings', 'pt_created_dt', 'pt_updated_dt'], 'safe'],
            [['pt_key'], 'string', 'max' => 20],
            [['pt_name'], 'string', 'max' => 50],
            [['pt_id'], 'unique'],
            [['pt_key'], 'unique'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'pt_id' => 'ID',
            'pt_key' => 'Key',
            'pt_name' => 'Name',
            'pt_service_fee_percent' => 'Service Fee percent',
            'pt_description' => 'Description',
            'pt_settings' => 'Settings',
            'pt_enabled' => 'Enabled',
            'pt_created_dt' => 'Created Dt',
            'pt_updated_dt' => 'Updated Dt',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }
}
