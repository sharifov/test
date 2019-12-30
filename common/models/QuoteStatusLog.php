<?php

namespace common\models;

use common\models\query\QuoteStatusLogQuery;
use Yii;

/**
 * This is the model class for table "quote_status_log".
 *
 * @property int $id
 * @property int $employee_id
 * @property int $quote_id
 * @property int $status
 * @property string $created
 *
 * @property Employee $employee
 * @property Quotes $quote
 */
class QuoteStatusLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'quote_status_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['employee_id', 'quote_id', 'status'], 'integer'],
            [['created'], 'safe'],
            [['employee_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['employee_id' => 'id']],
            [['quote_id'], 'exist', 'skipOnError' => true, 'targetClass' => Quote::class, 'targetAttribute' => ['quote_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'employee_id' => 'Employee ID',
            'quote_id' => 'Quote ID',
            'status' => 'Status',
            'created' => 'Created',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmployee()
    {
        return $this->hasOne(Employee::class, ['id' => 'employee_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuote()
    {
        return $this->hasOne(Quote::class, ['id' => 'quote_id']);
    }

    /**
     * {@inheritdoc}
     * @return QuoteStatusLogQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new QuoteStatusLogQuery(static::class);
    }


    public static function createNewFromQuote(Quote $quote)
    {
        $quoteStatusLog = new self();
        $quoteStatusLog->status = $quote->status;
        $quoteStatusLog->quote_id = $quote->id;
        if (!is_a(\Yii::$app, 'yii\console\Application') && !Yii::$app->user->isGuest && Yii::$app->user->identityClass != 'webapi\models\ApiUser') {
            $quoteStatusLog->employee_id = Yii::$app->user->identity->getId();
        }
        return $quoteStatusLog->save();
    }
}
