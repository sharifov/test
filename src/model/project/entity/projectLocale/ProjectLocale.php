<?php

namespace src\model\project\entity\projectLocale;

use common\models\Employee;
use common\models\Language;
use common\models\Project;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "project_locale".
 *
 * @property int $pl_id
 * @property int $pl_project_id
 * @property string|null $pl_language_id
 * @property string|null $pl_market_country
 * @property bool|null $pl_default
 * @property bool|null $pl_enabled
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
            [['pl_project_id'], 'required'], //, 'pl_language_id', 'pl_market_country'
            [['pl_project_id',  'pl_created_user_id', 'pl_updated_user_id'], 'integer'],
            [['pl_default', 'pl_enabled'], 'boolean'],
            [['pl_params', 'pl_created_dt', 'pl_updated_dt'], 'safe'],
            [['pl_language_id'], 'string', 'max' => 5],
            [['pl_market_country'], 'string', 'max' => 2],
            [['pl_language_id', 'pl_market_country'], 'default', 'value' => null],
            [['pl_project_id', 'pl_language_id', 'pl_market_country'], 'unique', 'targetAttribute' => ['pl_project_id', 'pl_language_id', 'pl_market_country']],
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
            'pl_id' => 'Id',
            'pl_project_id' => 'Project',
            'pl_language_id' => 'Locale',
            'pl_market_country' => 'Market Country',
            'pl_default' => 'Default',
            'pl_enabled' => 'Enabled',
            'pl_params' => 'Params',
            'pl_created_user_id' => 'Created User',
            'pl_updated_user_id' => 'Updated User',
            'pl_created_dt' => 'Created Date',
            'pl_updated_dt' => 'Updated Date',
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
                'createdByAttribute' => 'pl_created_user_id',
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

    public static function getLocaleListByProject(int $projectId): array
    {
        return ArrayHelper::map(
            self::find()
            ->select(['pl_language_id'])
            ->byProject($projectId)
            ->enabled()
            ->languageNotNull()
            ->orderBy(['pl_default' => SORT_DESC, 'pl_language_id' => SORT_ASC])
            ->asArray(true)
            ->all(),
            'pl_language_id',
            'pl_language_id',
            null
        );
    }

    public static function getEnabledLocaleListByProjectWithLanguageName(int $projectId): array
    {
        return ArrayHelper::map(
            self::find()
            ->select(['pl_language_id', 'name'])
            ->byProject($projectId)
            ->enabled()
            ->languageNotNull()
            ->joinWith('plLanguage')
            ->orderBy(['pl_language_id' => SORT_ASC])
            ->asArray(true)
            ->all(),
            'pl_language_id',
            'name',
            null
        );
    }

    public static function getDefaultLocaleByProject(int $projectId): ?string
    {
        return self::find()
            ->select(['pl_language_id'])
            ->byProject($projectId)
            ->default()
            ->enabled()
            ->languageNotNull()
            ->scalar();
    }

    public static function getDefaultMarketCountryByProject(int $projectId): ?string
    {
        return self::find()
            ->select(['pl_market_country'])
            ->byProject($projectId)
            ->andWhere(['pl_language_id' => null])
            ->andWhere(['IS NOT', 'pl_market_country', null])
            ->enabled()
            ->default()
            ->scalar();
    }

    /**
     * {@inheritdoc}
     * @return ProjectLocaleScopes the active query used by this AR class.
     */
    public static function find()
    {
        return new ProjectLocaleScopes(static::class);
    }

    /**
     * @param int $projectId
     * @param string|null $language
     * @param string|null $country
     * @return ProjectLocale|null
     */
    public static function getByProjectLanguageMarket(int $projectId, ?string $language, ?string $country): ?ProjectLocale
    {
        return self::find()
            ->byProject($projectId)
            ->byLanguage($language)
            ->byMarketCountry($country)
            ->enabled()
            ->one();
    }

    /**
     * @param int $projectId
     * @return ProjectLocale|null
     */
    public static function getDefaultProjectLocale(int $projectId): ?ProjectLocale
    {
        return self::find()
            ->byProject($projectId)
            ->andWhere(['pl_language_id' => null])
            ->andWhere(['IS NOT', 'pl_market_country', null])
            ->enabled()
            ->default()
            ->one();
    }
}
