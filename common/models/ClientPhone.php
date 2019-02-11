<?php

namespace common\models;

use Yii;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\VarDumper;
use yii\queue\Queue;
use common\components\CheckPhoneNumberJob;


/**
 * This is the model class for table "client_phone".
 *
 * @property int $id
 * @property int $client_id
 * @property string $phone
 * @property int $is_sms
 * @property string $validate_dt
 * @property string $created
 * @property string $updated
 * @property string $comments
 *
 * @property Client $client
 */
class ClientPhone extends \yii\db\ActiveRecord
{

    // old phone value. need for afterSave() method
    private $old_phone = '';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'client_phone';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['phone'], 'required'],
            [['client_id', 'is_sms'], 'integer'],
            [['created', 'updated', 'comments', 'validate_dt'], 'safe'],
            [['phone'], 'string', 'max' => 100],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Client::class, 'targetAttribute' => ['client_id' => 'id']],
            [['phone', 'client_id'], 'unique', 'targetAttribute' => ['phone', 'client_id']]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'client_id' => 'Client ID',
            'phone' => 'Phone',
            'is_sms' => 'Can send SMS',
            'validate_dt' => 'Validated at',
            'created' => 'Created',
            'updated' => 'Updated',
        ];
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created', 'updated'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClient()
    {
        return $this->hasOne(Client::class, ['id' => 'client_id']);
    }

    public function beforeValidate()
    {
        $this->phone = str_replace('-', '', $this->phone);
        $this->phone = str_replace(' ', '', $this->phone);
        if(!$this->isNewRecord) {
            $this->old_phone = $this->oldAttributes['phone'];
        }
        $this->updated = date('Y-m-d H:i:s');
        return parent::beforeValidate();
    }

    /**
     * @param string $phoneNumber
     * @return null|string|string[]
     */
    public static function clearNumber(string $phoneNumber = '')
    {
        $phoneNumber = preg_replace('~[^0-9\+]~', '', $phoneNumber);
        if(isset($phoneNumber[0])) {
            $phoneNumber = ($phoneNumber[0] === '+' ? '+' : '') . str_replace('+', '', $phoneNumber);
        }
        return $phoneNumber;
    }

    public function afterSave($insert, $changedAttributes)
    {
        if($this->id > 0 && $this->client_id > 0 ) {
            $isRenewPhoneNumber = ( $this->old_phone != '' && $this->old_phone !== $this->phone );
            /*\Yii::info(VarDumper::dumpAsString([
                'client_id' => $this->client_id,
                'id' => $this->id,
                'validate_dt' => $this->validate_dt,
                'is_sms' => $this->is_sms,
                'old_phone' => $this->old_phone,
                'phone' => $this->phone,
                'isRenewPhoneNumber' => $isRenewPhoneNumber,
            ]), 'info\model:ClientPhone:afterSave');*/

            // check if phone rewrite
            if(NULL === $this->validate_dt || $isRenewPhoneNumber) {
                /** @var Queue $queue */
                $queue = \Yii::$app->queue_phone_check;
                $job = new CheckPhoneNumberJob();
                $job->client_id = $this->client_id;
                $job->client_phone_id = $this->id;
                $queue->push($job);
            }
        }
        parent::afterSave($insert, $changedAttributes);
    }
}
