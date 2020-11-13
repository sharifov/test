<?php

namespace sales\model\clientChat\entity\channelTranslate;

use common\models\Employee;
use common\models\Language;
use sales\model\clientChatChannel\entity\ClientChatChannel;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "client_chat_channel_translate".
 *
 * @property int $ct_channel_id
 * @property string $ct_language_id
 * @property string $ct_name
 * @property int|null $ct_created_user_id
 * @property int|null $ct_updated_user_id
 * @property string|null $ct_created_dt
 * @property string|null $ct_updated_dt
 *
 * @property ClientChatChannel $ctChannel
 * @property Employee $ctCreatedUser
 * @property Language $ctLanguage
 * @property Employee $ctUpdatedUser
 */
class ClientChatChannelTranslate extends \yii\db\ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName()
    {
        return 'client_chat_channel_translate';
    }

    /**
     * @return array|array[]
     */
    public function rules()
    {
        return [
            [['ct_channel_id', 'ct_language_id', 'ct_name'], 'required'],
            [['ct_channel_id', 'ct_created_user_id', 'ct_updated_user_id'], 'integer'],
            [['ct_created_dt', 'ct_updated_dt'], 'safe'],
            [['ct_language_id'], 'string', 'max' => 5],
            [['ct_name'], 'string', 'max' => 100],
            [['ct_channel_id', 'ct_language_id'], 'unique', 'targetAttribute' => ['ct_channel_id', 'ct_language_id']],
            [['ct_channel_id'], 'exist', 'skipOnError' => true, 'targetClass' => ClientChatChannel::class, 'targetAttribute' => ['ct_channel_id' => 'ccc_id']],
            [['ct_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['ct_created_user_id' => 'id']],
            [['ct_language_id'], 'exist', 'skipOnError' => true, 'targetClass' => Language::class, 'targetAttribute' => ['ct_language_id' => 'language_id']],
            [['ct_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['ct_updated_user_id' => 'id']],
        ];
    }

    /**
     * @return array|string[]
     */
    public function attributeLabels()
    {
        return [
            'ct_channel_id' => 'Channel ID',
            'ct_language_id' => 'Language ID',
            'ct_name' => 'Name',
            'ct_created_user_id' => 'Created User ID',
            'ct_updated_user_id' => 'Updated User ID',
            'ct_created_dt' => 'Created Dt',
            'ct_updated_dt' => 'Updated Dt',
        ];
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        $behaviors = [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['ct_created_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['ct_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'ct_created_user_id',
                'updatedByAttribute' => 'ct_updated_user_id',
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * Gets query for [[CtChannel]].
     *
     * @return ActiveQuery
     */
    public function getCtChannel(): ActiveQuery
    {
        return $this->hasOne(ClientChatChannel::class, ['ccc_id' => 'ct_channel_id']);
    }

    /**
     * Gets query for [[CtCreatedUser]].
     *
     * @return ActiveQuery
     */
    public function getCtCreatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'ct_created_user_id']);
    }

    /**
     * Gets query for [[CtLanguage]].
     *
     * @return ActiveQuery
     */
    public function getCtLanguage(): ActiveQuery
    {
        return $this->hasOne(Language::class, ['language_id' => 'ct_language_id']);
    }

    /**
     * Gets query for [[CtUpdatedUser]].
     *
     * @return ActiveQuery
     */
    public function getCtUpdatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'ct_updated_user_id']);
    }
}
