<?php

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Class QuoteUrlActivity
 * @package common\models
 *
 * @property int $qua_id
 * @property string $qua_uid
 * @property int $qua_quote_id
 * @property int $qua_communication_type
 * @property int $qua_status
 * @property string|null $qua_ext_data
 * @property string|null $qua_created_dt
 *
 * @property Quote $quote
 */
class QuoteUrlActivity extends ActiveRecord
{
    const STATUS_NEW = 1;
    const STATUS_OPENED = 2;

    /**
     * @return array
     */
    public static function statusList(): array
    {
        return [
            self::STATUS_NEW => 'New',
            self::STATUS_OPENED => 'Opened'
        ];
    }

    /**
     * @param int $id
     * @return null|string
     */
    public static function statusName(int $id): ?string
    {
        return isset(self::statusList()[$id]) ? self::statusList()[$id] : null;
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
                    ActiveRecord::EVENT_BEFORE_INSERT => ['qua_created_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'quote_url_activity';
    }


    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['qua_uid', 'qua_quote_id', 'qua_communication_type', 'qua_status'], 'required'],
            [['qua_quote_id', 'qua_communication_type', 'qua_status'], 'integer'],
            [['qua_ext_data'], 'string'],
            [['qua_created_dt'], 'safe'],
            [['qua_quote_id'], 'exist', 'skipOnError' => true, 'targetClass' => Quote::class, 'targetAttribute' => ['qua_quote_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'qua_id' => 'ID',
            'qua_uid' => 'UUID',
            'qua_quote_id' => 'Quote ID',
            'qua_communication_type' => 'Communication Type',
            'qua_status' => 'Status',
            'qua_ext_data' => 'Ext data',
            'qua_created_dt' => 'Created Datetime'
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getQuote(): ActiveQuery
    {
        return $this->hasOne(Quote::class, ['id' => 'qc_quote_id']);
    }
}
