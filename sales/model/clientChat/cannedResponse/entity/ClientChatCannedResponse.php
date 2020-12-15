<?php

namespace sales\model\clientChat\cannedResponse\entity;

use common\models\Employee;
use common\models\Language;
use common\models\Project;
use sales\model\clientChat\cannedResponseCategory\entity\ClientChatCannedResponseCategory;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "client_chat_canned_response".
 *
 * @property int $cr_id
 * @property int|null $cr_project_id
 * @property int|null $cr_category_id
 * @property string|null $cr_language_id
 * @property int|null $cr_user_id
 * @property int|null $cr_sort_order
 * @property string|null $cr_message
 * @property string|null $cr_created_dt
 * @property string|null $cr_updated_dt
 *
 * @property ClientChatCannedResponseCategory $crCategory
 * @property Language $crLanguage
 * @property Project $crProject
 * @property Employee $crUser
 */
class ClientChatCannedResponse extends \yii\db\ActiveRecord
{
    public function rules(): array
    {
        return [
            ['cr_category_id', 'integer'],
            ['cr_category_id', 'exist', 'skipOnError' => true, 'targetClass' => ClientChatCannedResponseCategory::class, 'targetAttribute' => ['cr_category_id' => 'crc_id']],

            ['cr_created_dt', 'safe'],

            ['cr_language_id', 'string', 'max' => 5],
            ['cr_language_id', 'exist', 'skipOnError' => true, 'targetClass' => Language::class, 'targetAttribute' => ['cr_language_id' => 'language_id']],

            ['cr_message', 'string', 'max' => 1000],

            ['cr_project_id', 'integer'],
            ['cr_project_id', 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['cr_project_id' => 'id']],

            ['cr_sort_order', 'integer'],
            ['cr_sort_order', 'default', 'value' => 1],

            ['cr_updated_dt', 'safe'],

            ['cr_user_id', 'integer'],
            ['cr_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['cr_user_id' => 'id']],
        ];
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['cr_created_dt', 'cr_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['cr_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s'),
            ],
        ];
    }

    public function getCrCategory(): \yii\db\ActiveQuery
    {
        return $this->hasOne(ClientChatCannedResponseCategory::class, ['crc_id' => 'cr_category_id']);
    }

    public function getCrLanguage(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Language::class, ['language_id' => 'cr_language_id']);
    }

    public function getCrProject(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Project::class, ['id' => 'cr_project_id']);
    }

    public function getCrUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'cr_user_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'cr_id' => 'ID',
            'cr_project_id' => 'Project ID',
            'cr_category_id' => 'Category ID',
            'cr_language_id' => 'Language ID',
            'cr_user_id' => 'User ID',
            'cr_sort_order' => 'Sort Order',
            'cr_message' => 'Message',
            'cr_created_dt' => 'Created Dt',
            'cr_updated_dt' => 'Updated Dt',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return 'client_chat_canned_response';
    }

    /**
     * @return object
     */
    public static function getDb()
    {
        return \Yii::$app->get('db_postgres');
    }
}
