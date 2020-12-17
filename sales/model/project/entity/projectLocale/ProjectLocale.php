<?php
namespace sales\model\project\entity\projectLocale;

use common\models\Employee;
use common\models\Language;
use common\models\Project;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "project_locale".
 *
 * @property int $pl_project_id
 * @property string $pl_language_id
 * @property int|null $pl_default
 * @property int|null $pl_enabled
 * @property string|null $pl_params
 * @property int|null $pl_created_user_id
 * @property int|null $pl_updated_user_id
 * @property string|null $pl_created_dt
 * @property string|null $pl_updated_dt
 *
 * @property Employee $plCreatedUser
 * @property Language $plLanguage
 * @property Project $plProject
 * @property Employee $plUpdatedUser
 */
class ProjectLocale extends \yii\db\ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName()
    {
        return 'project_locale';
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['pl_project_id', 'pl_language_id'], 'required'],
            [['pl_project_id', 'pl_default', 'pl_enabled', 'pl_created_user_id', 'pl_updated_user_id'], 'integer'],
            [['pl_params', 'pl_created_dt', 'pl_updated_dt'], 'safe'],
            [['pl_language_id'], 'string', 'max' => 5],
            [['pl_project_id', 'pl_language_id'], 'unique', 'targetAttribute' => ['pl_project_id', 'pl_language_id']],
            [['pl_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['pl_created_user_id' => 'id']],
            [['pl_language_id'], 'exist', 'skipOnError' => true, 'targetClass' => Language::class, 'targetAttribute' => ['pl_language_id' => 'language_id']],
            [['pl_project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['pl_project_id' => 'id']],
            [['pl_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['pl_updated_user_id' => 'id']],
        ];
    }

    /**
     * @return string[]
     */
    public function attributeLabels()
    {
        return [
            'pl_project_id' => 'Project ID',
            'pl_language_id' => 'Locale ID',
            'pl_default' => 'Default',
            'pl_enabled' => 'Enabled',
            'pl_params' => 'Params',
            'pl_created_user_id' => 'Created User',
            'pl_updated_user_id' => 'Updated User',
            'pl_created_dt' => 'Created Dt',
            'pl_updated_dt' => 'Updated Dt',
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
                    ActiveRecord::EVENT_BEFORE_INSERT => ['pl_created_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['pl_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'pl_updated_user_id',
                'updatedByAttribute' => 'pl_updated_user_id',
            ],
        ];
    }

    /**
     * Gets query for [[PlCreatedUser]].
     *
     * @return ActiveQuery
     */
    public function getPlCreatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'pl_created_user_id']);
    }

    /**
     * Gets query for [[PlLanguage]].
     *
     * @return ActiveQuery
     */
    public function getPlLanguage(): ActiveQuery
    {
        return $this->hasOne(Language::class, ['language_id' => 'pl_language_id']);
    }

    /**
     * Gets query for [[PlProject]].
     *
     * @return ActiveQuery
     */
    public function getPlProject(): ActiveQuery
    {
        return $this->hasOne(Project::class, ['id' => 'pl_project_id']);
    }

    /**
     * Gets query for [[PlUpdatedUser]].
     *
     * @return ActiveQuery
     */
    public function getPlUpdatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'pl_updated_user_id']);
    }
}
