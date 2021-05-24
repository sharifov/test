<?php

namespace sales\model\appProjectKey\entity;

use common\models\Employee;
use common\models\Project;
use common\models\Sources;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "app_project_key".
 *
 * @property int $apk_id
 * @property string|null $apk_key
 * @property int $apk_project_id
 * @property int $apk_project_source_id
 * @property string|null $apk_created_dt
 * @property string|null $apk_updated_dt
 * @property int|null $apk_created_user_id
 * @property int|null $apk_updated_user_id
 *
 * @property Employee $apkCreatedUser
 * @property Project $apkProject
 * @property Sources $apkProjectSource
 * @property Employee $apkUpdatedUser
 */
class AppProjectKey extends \yii\db\ActiveRecord
{
    public function rules(): array
    {
        return [
            ['apk_key', 'string', 'max' => 50],
            ['apk_key', 'unique'],

            ['apk_project_id', 'required'],
            ['apk_project_id', 'integer'],
            ['apk_project_id', 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['apk_project_id' => 'id']],

            ['apk_project_source_id', 'required'],
            ['apk_project_source_id', 'integer'],
            ['apk_project_source_id', 'exist', 'skipOnError' => true, 'targetClass' => Sources::class, 'targetAttribute' => ['apk_project_source_id' => 'id']],

            [['apk_updated_dt', 'apk_created_dt'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],

            [['apk_updated_user_id', 'apk_created_user_id'], 'integer'],
            [['apk_updated_user_id', 'apk_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['apk_updated_user_id' => 'id']],
        ];
    }

    public function getApkCreatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'apk_created_user_id']);
    }

    public function getApkProject(): ActiveQuery
    {
        return $this->hasOne(Project::class, ['id' => 'apk_project_id']);
    }

    public function getApkProjectSource(): ActiveQuery
    {
        return $this->hasOne(Sources::class, ['id' => 'apk_project_source_id']);
    }

    public function getApkUpdatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'apk_updated_user_id']);
    }

    public function behaviors(): array
    {
        $behaviors = [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['apk_created_dt', 'apk_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['apk_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['apk_created_user_id'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['apk_updated_user_id'],
                ]
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function attributeLabels(): array
    {
        return [
            'apk_id' => 'ID',
            'apk_key' => 'Key',
            'apk_project_id' => 'Project ID',
            'apk_project_source_id' => 'Project Source ID',
            'apk_created_dt' => 'Created Dt',
            'apk_updated_dt' => 'Updated Dt',
            'apk_created_user_id' => 'Created User ID',
            'apk_updated_user_id' => 'Updated User ID',
        ];
    }

    public static function find(): AppProjectKeyScopes
    {
        return new AppProjectKeyScopes(static::class);
    }

    public static function tableName(): string
    {
        return 'app_project_key';
    }
}
