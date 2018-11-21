<?php

namespace common\models;

use common\components\CommunicationService;
use Yii;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "email_template_type".
 *
 * @property int $etp_id
 * @property string $etp_key
 * @property string $etp_name
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
            [['etp_key', 'etp_name'], 'required'],
            [['etp_created_user_id', 'etp_updated_user_id'], 'integer'],
            [['etp_created_dt', 'etp_updated_dt'], 'safe'],
            [['etp_key'], 'string', 'max' => 50],
            [['etp_name'], 'string', 'max' => 100],
            [['etp_key'], 'unique'],
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
            'etp_created_user_id' => 'Created User ID',
            'etp_updated_user_id' => 'Updated User ID',
            'etp_created_dt' => 'Created Dt',
            'etp_updated_dt' => 'Updated Dt',
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
        $mailTypes = $communication->mailTypes(7);
        //VarDumper::dump($mailTypes, 10, true);  //exit;

        if($mailTypes && isset($mailTypes['data']['types'])) {

                foreach ($mailTypes['data']['types'] as $type) {
                    $t = self::findOne($type['etp_id']);
                    if(!$t) {
                        $t = new self();
                        $t->etp_id = $type['etp_id'];
                        $t->etp_created_dt = date('Y-m-d H:i:s');

                        if(isset(Yii::$app->user) && Yii::$app->user->id) {
                            $t->etp_created_user_id = Yii::$app->user->id;
                        }

                        $data['created'][] = $type['etp_id'];
                    } else {
                        $data['updated'][] = $type['etp_id'];
                    }

                    $t->etp_key = $type['etp_key'];
                    $t->etp_name = $type['etp_name'];
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
