<?php

namespace common\models;

use common\components\CommunicationService;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "email_template_type".
 *
 * @property int $etp_id
 * @property string $etp_key
 * @property string $etp_name
 * @property string $etp_origin_name
 * @property boolean $etp_hidden
 * @property int $etp_created_user_id
 * @property int $etp_updated_user_id
 * @property string $etp_created_dt
 * @property string $etp_updated_dt
 *
 * @property Email[] $emails
 * @property Employee $etpCreatedUser
 * @property Employee $etpUpdatedUser
 */
class EmailTemplateType extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'email_template_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['etp_key', 'etp_name', 'etp_origin_name'], 'required'],
            [['etp_created_user_id', 'etp_updated_user_id'], 'integer'],
            [['etp_created_dt', 'etp_updated_dt'], 'safe'],
            [['etp_key'], 'string', 'max' => 50],
            [['etp_name', 'etp_origin_name'], 'string', 'max' => 100],
            [['etp_key'], 'unique'],
            [['etp_hidden'], 'boolean'],
            [['etp_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['etp_created_user_id' => 'id']],
            [['etp_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['etp_updated_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'etp_id' => 'ID',
            'etp_key' => 'Key',
            'etp_name' => 'Name',
            'etp_origin_name' => 'Original Name',
            'etp_hidden'    => 'Hidden',
            'etp_created_user_id' => 'Created User ID',
            'etp_updated_user_id' => 'Updated User ID',
            'etp_created_dt' => 'Created Dt',
            'etp_updated_dt' => 'Updated Dt',
        ];
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
                    ActiveRecord::EVENT_BEFORE_INSERT => ['etp_created_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['etp_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'etp_created_user_id',
                'updatedByAttribute' => 'etp_updated_user_id',
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmails()
    {
        return $this->hasMany(Email::class, ['e_template_type_id' => 'etp_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEtpCreatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'etp_created_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEtpUpdatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'etp_updated_user_id']);
    }

    /**
     * {@inheritdoc}
     * @return EmailTemplateTypeQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new EmailTemplateTypeQuery(get_called_class());
    }


    /**
     * @return array
     * @throws \yii\httpclient\Exception
     */
    public static function synchronizationTypes() : array
    {
        $data = [
            'created' => [],
            'updated' => [],
            'error' => false
        ];

        /** @var CommunicationService $communication */
        $communication = Yii::$app->communication;
        $mailTypes = $communication->mailTypes();

        if($mailTypes && isset($mailTypes['data']['types'])) {

                foreach ($mailTypes['data']['types'] as $type) {
                    $t = self::findOne($type['etp_id']);
                    if(!$t) {
                        $t = new self();
                        $t->etp_id = $type['etp_id'];
                        $t->etp_created_dt = date('Y-m-d H:i:s');
                        $t->etp_name = $type['etp_name'];

                        if(isset(Yii::$app->user) && Yii::$app->user->id) {
                            $t->etp_created_user_id = Yii::$app->user->id;
                        }

                        $data['created'][] = $type['etp_id'];
                    } else {
                        $data['updated'][] = $type['etp_id'];
                    }

                    $t->etp_key = $type['etp_key'];
                    $t->etp_origin_name = $type['etp_name'];
                    $t->etp_updated_dt = date('Y-m-d H:i:s');

                    if(isset(Yii::$app->user) && Yii::$app->user->id) {
                        $t->etp_updated_user_id = Yii::$app->user->id;
                    }
                    if(!$t->save()) {
                        Yii::error(VarDumper::dumpAsString($t->errors), 'EmailTemplateType:synchronizationTypes:save');
                    }
                }

        } else {
            $data['error'] = 'Not found response data';
        }

        return $data;
    }
}
