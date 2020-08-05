<?php

namespace common\models;

use lajax\translatemanager\models\LanguageSource;
use lajax\translatemanager\models\LanguageTranslate;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "language".
 *
 * @property string $language_id
 * @property string $language
 * @property string $country
 * @property string $name
 * @property string $name_ascii
 * @property int $status
 *
 * @property Email[] $emails
 * @property LanguageTranslate[] $languageTranslates
 * @property LanguageSource[] $ids
 * @property Lead[] $leads
 */
class Language extends ActiveRecord
{

    /**
     * Status of inactive language.
     */
    const STATUS_INACTIVE = 0;

    /**
     * Status of active language.
     */
    const STATUS_ACTIVE = 1;

    /**
     * Status of ‘beta’ language.
     */
    const STATUS_BETA = 2;

    /**
     * Array containing possible states.
     *
     * @var array
     * @translate
     */
    private static $_CONDITIONS = [
        self::STATUS_INACTIVE => 'Inactive',
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_BETA => 'Beta',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'language';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['language_id', 'language', 'country', 'name', 'name_ascii', 'status'], 'required'],
            [['status'], 'integer'],
            [['language_id'], 'string', 'max' => 5],
            [['language', 'country'], 'string', 'max' => 3],
            [['name', 'name_ascii'], 'string', 'max' => 32],
            [['language_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'language_id' => 'Language ID',
            'language' => 'Language',
            'country' => 'Country',
            'name' => 'Name',
            'name_ascii' => 'Name Ascii',
            'status' => 'Status',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getEmails()
    {
        return $this->hasMany(Email::class, ['e_language_id' => 'language_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getLanguageTranslates()
    {
        return $this->hasMany(LanguageTranslate::class, ['language' => 'language_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getIds()
    {
        return $this->hasMany(LanguageSource::class, ['id' => 'id'])->viaTable('language_translate', ['language' => 'language_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getLeads(): ActiveQuery
    {
        return $this->hasMany(Lead::class, ['l_client_lang' => 'language_id']);
    }

    /**
     * @return array
     */
    public static function getList(): array
	{
        return ArrayHelper::map(
            self::find()->orderBy(['name' => SORT_ASC])->asArray()->all(), 'language_id', 'name');
	}

	/**
	 * @param array $languages
	 * @return array
	 */
	public static function getListByPk(array $languages): array
	{
		return ArrayHelper::map(
			self::find()->where(['IN', 'language_id', $languages])->orderBy(['name' => SORT_ASC])->asArray()->all(), 'language_id', 'name');
	}

    /**
     * @param bool $active
     * @param null $group
     * @return array
     */
    public static function getLanguages($active = true, $group = null): array
    {
        if ($active) {
            $query = self::find()->where(['status' => static::STATUS_ACTIVE]);
        } else {
            $query =  self::find();
        }

        $data = ArrayHelper::map($query->asArray(true)->all(), 'language_id', 'name', $group);

        return $data;
    }

//    /**
//     * @param bool $active
//     * @return array
//     */
//    public static function getLanguageNames($active = false): array
//    {
//        $languageNames = [];
//        foreach (self::getLanguages($active, true) as $language) {
//            $languageNames[$language['language_id']] = $language['name'];
//        }
//
//        return $languageNames;
//    }
}
