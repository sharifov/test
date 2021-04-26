<?php

namespace modules\fileStorage\src\entity\fileProductQuote;

use modules\fileStorage\src\entity\fileStorage\FileStorage;
use modules\product\src\entities\productQuote\ProductQuote;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%file_product_quote}}".
 *
 * @property int $fpq_fs_id
 * @property int $fpq_pq_id
 * @property string|null $fpq_created_dt
 *
 * @property FileStorage $file
 */
class FileProductQuote extends \yii\db\ActiveRecord
{
    public function rules(): array
    {
        return [
            [['fpq_fs_id', 'fpq_pq_id'], 'unique', 'targetAttribute' => ['fpq_fs_id', 'fpq_pq_id']],

            ['fpq_fs_id', 'required'],
            ['fpq_fs_id', 'integer', 'min' => 1, 'max' => 2147483647, 'tooBig' => '{attribute} is out of range for type integer'],
            ['fpq_fs_id', 'exist', 'skipOnError' => true, 'targetClass' => FileStorage::class, 'targetAttribute' => ['fpq_fs_id' => 'fs_id']],

            ['fpq_pq_id', 'required'],
            ['fpq_pq_id', 'integer', 'min' => 1, 'max' => 2147483647, 'tooBig' => '{attribute} is out of range for type integer'],
            ['fpq_pq_id', 'exist', 'skipOnError' => true, 'targetClass' => ProductQuote::class, 'targetAttribute' => ['fpq_pq_id' => 'pq_id']],

            [['fpq_created_dt'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],
        ];
    }

    public function behaviors(): array
    {
        $behaviors = [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['fpq_created_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function attributeLabels(): array
    {
        return [
            'fpq_fs_id' => 'File ID',
            'fpq_pq_id' => 'Product Quote ID',
            'fpq_created_dt' => 'Created Dt',
        ];
    }

    public function getFile(): \yii\db\ActiveQuery
    {
        return $this->hasOne(FileStorage::class, ['fs_id' => 'fpq_fs_id']);
    }

    public static function tableName(): string
    {
        return '{{%file_product_quote}}';
    }

    public static function getDb(): \yii\db\Connection
    {
        return Yii::$app->get('db_postgres');
    }

    public static function create(int $fileId, int $productQuoteId): self
    {
        $model = new static();
        $model->fpq_fs_id = $fileId;
        $model->fpq_pq_id = $productQuoteId;
        return $model;
    }

    public static function find(): FileProductQuoteScopes
    {
        return new FileProductQuoteScopes(static::class);
    }
}
