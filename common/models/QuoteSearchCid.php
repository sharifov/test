<?php

namespace common\models;

use common\models\query\QuoteQuery;
use common\models\query\QuoteSearchCidScopes;

/**
 * This is the model class for table "quote_search_cid".
 *
 * @property int $qsc_id
 * @property int|null $qsc_q_id
 * @property string|null $qsc_cid
 *
 * @property Quote $qscQ
 */
class QuoteSearchCid extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'quote_search_cid';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['qsc_q_id'], 'integer'],
            [['qsc_cid'], 'string', 'max' => 255],
            [['qsc_q_id'], 'exist', 'skipOnError' => true, 'targetClass' => Quote::class, 'targetAttribute' => ['qsc_q_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'qsc_id' => 'ID',
            'qsc_q_id' => 'Quote ID',
            'qsc_cid' => 'Cid',
        ];
    }

    /**
     * Gets query for [[QscQ]].
     *
     * @return \yii\db\ActiveQuery|QuoteQuery
     */
    public function getQscQ()
    {
        return $this->hasOne(Quote::class, ['id' => 'qsc_q_id']);
    }

    /**
     * {@inheritdoc}
     * @return QuoteSearchCidScopes the active query used by this AR class.
     */
    public static function find(): QuoteSearchCidScopes
    {
        return new QuoteSearchCidScopes(get_called_class());
    }

    public static function create(int $quoteId, string $cid): self
    {
        $model = new self();
        $model->qsc_q_id = $quoteId;
        $model->qsc_cid = $cid;

        return $model;
    }
}
