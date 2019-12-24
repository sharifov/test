<?php

namespace common\models;

use common\models\query\ProductTypeQuery;
use Yii;
use yii\helpers\ArrayHelper;

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

    public const PRODUCT_FLIGHT = 1;
    public const PRODUCT_HOTEL  = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
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

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
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

    /**
     * {@inheritdoc}
     * @return ProductTypeQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ProductTypeQuery(get_called_class());
    }

    /**
     * @param bool $enabled
     * @return array
     */
    public static function getList(bool $enabled = true) : array
    {
        $query = self::find()->orderBy(['pt_id' => SORT_ASC]);
        if ($enabled) {
            $query->andWhere(['pt_enabled' => true]);
        }
        $data = $query->asArray()->all();
        return ArrayHelper::map($data, 'pt_id', 'pt_name');
    }
}
