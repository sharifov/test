<?php

namespace modules\product\src\entities\productQuoteRelation;

use common\models\Employee;
use modules\product\src\entities\productQuote\ProductQuote;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "product_quote_relation".
 *
 * @property int $pqr_parent_pq_id
 * @property int $pqr_related_pq_id
 * @property int $pqr_type_id 1 - replace, 2 - clone
 * @property int|null $pqr_created_user_id
 * @property string|null $pqr_created_dt
 *
 * @property Employee $pqrCreatedUser
 * @property ProductQuote $pqrParentPq
 * @property ProductQuote $pqrRelatedPq
 */
class ProductQuoteRelation extends \yii\db\ActiveRecord
{
    public const TYPE_REPLACE = 1;
    public const TYPE_CLONE = 2;

    public const TYPE_LIST = [
        self::TYPE_REPLACE => 'Replace',
        self::TYPE_CLONE => 'Clone',
    ];

    public static function tableName(): string
    {
        return 'product_quote_relation';
    }

    public function rules(): array
    {
        return [
            [['pqr_parent_pq_id', 'pqr_related_pq_id', 'pqr_type_id'], 'unique', 'targetAttribute' => ['pqr_parent_pq_id', 'pqr_related_pq_id', 'pqr_type_id']],

            ['pqr_parent_pq_id', 'required'],
            ['pqr_parent_pq_id', 'integer'],
            ['pqr_parent_pq_id', 'exist', 'skipOnError' => true, 'targetClass' => ProductQuote::class, 'targetAttribute' => ['pqr_parent_pq_id' => 'pq_id']],

            ['pqr_related_pq_id', 'required'],
            ['pqr_related_pq_id', 'integer'],
            ['pqr_related_pq_id', 'exist', 'skipOnError' => true, 'targetClass' => ProductQuote::class, 'targetAttribute' => ['pqr_related_pq_id' => 'pq_id']],

            ['pqr_type_id', 'required'],
            ['pqr_type_id', 'integer'],
            ['pqr_type_id', 'in', 'range' => array_keys(self::TYPE_LIST)],

            ['pqr_created_dt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],

            ['pqr_created_user_id', 'integer'],
            ['pqr_created_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['pqr_created_user_id' => 'id']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'pqr_parent_pq_id' => 'Parent Product Quote',
            'pqr_related_pq_id' => 'Related Product Quote',
            'pqr_type_id' => 'Type',
            'pqr_created_user_id' => 'Created User',
            'pqr_created_dt' => 'Created Dt',
        ];
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['pqr_created_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['pqr_created_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ],
        ];
    }

    public function getPqrCreatedUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'pqr_created_user_id']);
    }

    public function getPqrParentPq(): \yii\db\ActiveQuery
    {
        return $this->hasOne(ProductQuote::class, ['pq_id' => 'pqr_parent_pq_id']);
    }

    public function getPqrRelatedPq(): \yii\db\ActiveQuery
    {
        return $this->hasOne(ProductQuote::class, ['pq_id' => 'pqr_related_pq_id']);
    }

    public static function find(): ProductQuoteRelationScopes
    {
        return new ProductQuoteRelationScopes(static::class);
    }

    public static function getTypeName($typeId): string
    {
        return self::TYPE_LIST[$typeId] ?? '-';
    }

    /**
     * @param int $parentId
     * @param int $relatedId
     * @param int|null $userId
     * @return ProductQuoteRelation
     */
    public static function replace(int $parentId, int $relatedId, ?int $userId = null): ProductQuoteRelation
    {
        $model = new self();
        $model->pqr_parent_pq_id = $parentId;
        $model->pqr_related_pq_id = $relatedId;
        $model->pqr_created_user_id = $userId;
        $model->pqr_type_id = self::TYPE_REPLACE;
        return $model;
    }

    public static function clone(int $parentId, int $relatedId, ?int $userId = null): ProductQuoteRelation
    {
        $model = new self();
        $model->pqr_parent_pq_id = $parentId;
        $model->pqr_related_pq_id = $relatedId;
        $model->pqr_created_user_id = $userId;
        $model->pqr_type_id = self::TYPE_CLONE;
        return $model;
    }

    public static function isReplace(int $productQuoteId): bool
    {
        return self::find()
            ->where(['pqr_related_pq_id' => $productQuoteId])
            ->andWhere(['pqr_type_id' => self::TYPE_REPLACE])
            ->exists();
    }

    public static function isClone(int $productQuoteId): bool
    {
        return self::find()
            ->where(['pqr_related_pq_id' => $productQuoteId])
            ->andWhere(['pqr_type_id' => self::TYPE_CLONE])
            ->exists();
    }
}
