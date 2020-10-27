<?php

namespace sales\model\clientChat\cannedResponseCategory\entity;

use common\models\Employee;
use sales\model\clientChat\cannedResponse\entity\ClientChatCannedResponse;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "client_chat_canned_response_category".
 *
 * @property int $crc_id
 * @property string $crc_name
 * @property int|null $crc_enabled
 * @property string|null $crc_created_dt
 * @property string|null $crc_updated_dt
 * @property int|null $crc_created_user_id
 * @property int|null $crc_updated_user_id
 *
 * @property ClientChatCannedResponse[] $clientChatCannedResponses
 * @property Employee $crcCreatedUser
 * @property Employee $crcUpdatedUser
 */
class ClientChatCannedResponseCategory extends \yii\db\ActiveRecord
{
    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['crc_created_dt', 'crc_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['crc_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['crc_created_user_id'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['crc_updated_user_id'],
                ]
            ],
        ];
    }

    public function rules(): array
    {
        return [
            ['crc_created_dt', 'safe'],

            ['crc_created_user_id', 'integer'],
            ['crc_created_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['crc_created_user_id' => 'id']],

            ['crc_enabled', 'integer'],

            ['crc_name', 'required'],
            ['crc_name', 'string', 'max' => 50],

            ['crc_updated_dt', 'safe'],

            ['crc_updated_user_id', 'integer'],
            ['crc_updated_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['crc_updated_user_id' => 'id']],
        ];
    }

    public function getClientChatCannedResponses(): \yii\db\ActiveQuery
    {
        return $this->hasMany(ClientChatCannedResponse::class, ['cr_category_id' => 'crc_id']);
    }

    public function getCrcCreatedUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'crc_created_user_id']);
    }

    public function getCrcUpdatedUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'crc_updated_user_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'crc_id' => 'Crc ID',
            'crc_name' => 'Crc Name',
            'crc_enabled' => 'Crc Enabled',
            'crc_created_dt' => 'Crc Created Dt',
            'crc_updated_dt' => 'Crc Updated Dt',
            'crc_created_user_id' => 'Crc Created User ID',
            'crc_updated_user_id' => 'Crc Updated User ID',
        ];
    }

    public static function tableName(): string
    {
        return 'client_chat_canned_response_category';
    }

    /**
     * @return array
     */
    public static function getList() : array
    {
        $data = self::find()->orderBy(['crc_name' => SORT_ASC])->asArray()->all();
        return ArrayHelper::map($data, 'crc_id', 'crc_name');
    }

    /**
     * @return object
     */
    public static function getDb()
    {
        return \Yii::$app->get('db_postgres');
    }
}
