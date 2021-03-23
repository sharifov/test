<?php

namespace modules\fileStorage\src\entity\fileOrder;

use modules\fileStorage\src\entity\fileOrder\FileOrderScopes;
use modules\fileStorage\src\entity\fileStorage\FileStorage;
use modules\order\src\entities\order\Order;
use modules\product\src\entities\productQuote\ProductQuote;
use Yii;

/**
 * This is the model class for table "{{%file_order}}".
 *
 * @property int $fo_id
 * @property int $fo_fs_id
 * @property int $fo_or_id
 * @property int $fo_pq_id
 * @property int|null $fo_category_id
 * @property string|null $fo_created_dt
 *
 * @property FileStorage $file
 */
class FileOrder extends \yii\db\ActiveRecord
{
    public const CATEGORY_INVOICE = 1;
    public const CATEGORY_RECEIPT = 2;
    public const CATEGORY_CONFIRMATION = 3;

    public const CATEGORY_LIST = [
        self::CATEGORY_INVOICE => 'invoice',
        self::CATEGORY_RECEIPT => 'receipt',
        self::CATEGORY_CONFIRMATION => 'confirmation',
    ];

    public function rules(): array
    {
        return [
            ['fo_fs_id', 'required'],
            ['fo_fs_id', 'integer', 'min' => 1, 'max' => 2147483647, 'tooBig' => '{attribute} is out of range for type integer'],
            ['fo_fs_id', 'exist', 'skipOnError' => true, 'targetClass' => FileStorage::class, 'targetAttribute' => ['fo_fs_id' => 'fs_id']],

            ['fo_or_id', 'required'],
            ['fo_or_id', 'integer', 'min' => 1, 'max' => 2147483647, 'tooBig' => '{attribute} is out of range for type integer'],
            ['fo_or_id', 'exist', 'skipOnError' => true, 'targetClass' => Order::class, 'targetAttribute' => ['fo_or_id' => 'or_id']],


            ['fo_pq_id', 'integer'],
            ['fo_pq_id', 'exist', 'skipOnError' => true, 'skipOnEmpty' => true, 'targetClass' => ProductQuote::class, 'targetAttribute' => ['fo_pq_id' => 'pq_id']],

            ['fo_category_id', 'required'],
            ['fo_category_id', 'integer'],
            ['fo_category_id', 'in', 'range' => array_keys(self::CATEGORY_LIST)],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'fo_fs_id' => 'File ID',
            'fo_or_id' => 'Order ID',
            'fo_pq_id' => 'ProductQuote ID',
            'fo_category_id' => 'Category',
        ];
    }

    public function getFile(): \yii\db\ActiveQuery
    {
        return $this->hasOne(FileStorage::class, ['fs_id' => 'fo_fs_id']);
    }

    public static function find(): FileOrderScopes
    {
        return new FileOrderScopes(static::class);
    }

    public static function tableName(): string
    {
        return '{{%file_order}}';
    }

    public static function getDb(): \yii\db\Connection
    {
        return Yii::$app->get('db_postgres');
    }

    public static function create(int $fileId, int $orderId, ?int $productQuoteId, int $fo_category_id): self
    {
        $model = new static();
        $model->fo_fs_id = $fileId;
        $model->fo_or_id = $orderId;
        $model->fo_pq_id = $productQuoteId;
        $model->fo_category_id = $fo_category_id;
        $model->fo_created_dt = date('Y-m-d H:i:s');
        return $model;
    }

    public static function getCategoryName(?int $fo_category_id): string
    {
        return self::CATEGORY_LIST[$fo_category_id] ?? '';
    }
}
