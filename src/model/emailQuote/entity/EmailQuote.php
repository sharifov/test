<?php

namespace src\model\emailQuote\entity;

use common\models\Email;
use common\models\Employee;
use common\models\query\EmailQuery;
use common\models\query\EmployeeQuery;
use common\models\query\QuoteQuery;
use common\models\Quote;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "email_quote".
 *
 * @property int $eq_id
 * @property int $eq_email_id
 * @property int $eq_quote_id
 * @property string|null $eq_created_dt
 * @property int|null $eq_created_by
 *
 * @property Employee $createdBy
 * @property Email $email
 * @property Quote $quote
 */
class EmailQuote extends \yii\db\ActiveRecord
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
                    ActiveRecord::EVENT_BEFORE_INSERT => ['eq_created_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'eq_created_by',
                'updatedByAttribute' => false,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'email_quote';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['eq_email_id', 'eq_quote_id'], 'required'],
            [['eq_email_id', 'eq_quote_id', 'eq_created_by'], 'integer'],
            [['eq_created_dt'], 'safe'],
            [['eq_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['eq_created_by' => 'id']],
            [['eq_email_id'], 'exist', 'skipOnError' => true, 'targetClass' => Email::class, 'targetAttribute' => ['eq_email_id' => 'e_id']],
            [['eq_quote_id'], 'exist', 'skipOnError' => true, 'targetClass' => Quote::class, 'targetAttribute' => ['eq_quote_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'eq_id' => 'ID',
            'eq_email_id' => 'Email ID',
            'eq_quote_id' => 'Quote ID',
            'eq_created_dt' => 'Created Dt',
            'eq_created_by' => 'Created By',
        ];
    }

    /**
     * Gets query for [[EqCreatedBy]].
     *
     * @return \yii\db\ActiveQuery|EmployeeQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(Employee::class, ['id' => 'eq_created_by']);
    }

    /**
     * Gets query for [[EqEmail]].
     *
     * @return \yii\db\ActiveQuery|EmailQuery
     */
    public function getEmail()
    {
        return $this->hasOne(Email::class, ['e_id' => 'eq_email_id']);
    }

    /**
     * Gets query for [[EqQuote]].
     *
     * @return \yii\db\ActiveQuery|QuoteQuery
     */
    public function getQuote()
    {
        return $this->hasOne(Quote::class, ['id' => 'eq_quote_id']);
    }

    /**
     * {@inheritdoc}
     * @return Scopes the active query used by this AR class.
     */
    public static function find()
    {
        return new Scopes(get_called_class());
    }

    public static function create(int $emailId, int $quoteId): self
    {
        $self = new self();
        $self->eq_email_id = $emailId;
        $self->eq_quote_id = $quoteId;
        return $self;
    }
}
