<?php

namespace common\models;

use common\models\query\DepartmentQuery;
use src\model\department\department\Params;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\bootstrap4\Html;
use yii\caching\TagDependency;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * This is the model class for table "department".
 *
 * @property int $dep_id
 * @property string $dep_key
 * @property string $dep_name
 * @property int $dep_updated_user_id
 * @property string $dep_updated_dt
 * @property string $dep_params
 *
 * @property Call[] $calls
 * @property Employee $depUpdatedUser
 * @property DepartmentPhoneProject[] $departmentPhoneProjects
 * @property Lead[] $leads
 * @property UserDepartment[] $userDepartments
 */
class Department extends \yii\db\ActiveRecord
{
    public const DEPARTMENT_SALES       = 1;
    public const DEPARTMENT_EXCHANGE    = 2;
    public const DEPARTMENT_SUPPORT     = 3;
    public const DEPARTMENT_SCHEDULE_CHANGE = 4;
    public const DEPARTMENT_FRAUD_PREVENTION = 5;
    public const DEPARTMENT_CHAT = 6;
    public const DEPARTMENT_CROSS_SELL = 7;

    public const DEPARTMENT_LIST = [
        self::DEPARTMENT_SALES      => 'Sales',
        self::DEPARTMENT_EXCHANGE   => 'Exchange',
        self::DEPARTMENT_SUPPORT    => 'Support',
        self::DEPARTMENT_SCHEDULE_CHANGE    => 'Schedule Change',
        self::DEPARTMENT_FRAUD_PREVENTION => 'Fraud prevention',
        self::DEPARTMENT_CHAT => 'Chat',
        self::DEPARTMENT_CROSS_SELL => 'Cross Sell',
    ];

    private const CSS_CLASS_LIST = [
        self::DEPARTMENT_SALES      => 'success',
        self::DEPARTMENT_EXCHANGE   => 'warning',
        self::DEPARTMENT_SUPPORT    => 'info',
        self::DEPARTMENT_SCHEDULE_CHANGE    => 'primary',
        self::DEPARTMENT_FRAUD_PREVENTION    => 'secondary',
        self::DEPARTMENT_CHAT    => 'primary',
        self::DEPARTMENT_CROSS_SELL    => 'info',
    ];

    public const CACHE_KEY = 'department';
    public const CACHE_TAG_DEPENDENCY = 'department-tag-dependency';

    private static function getCssClass(?int $value): string
    {
        return self::CSS_CLASS_LIST[$value] ?? 'secondary';
    }

    public static function asFormat(?int $value): string
    {
        return Html::tag(
            'span',
            self::getName($value),
            ['class' => 'badge badge-' . self::getCssClass($value)]
        );
    }

    /**
     * @return bool
     */
    public function isSales(): bool
    {
        return $this->dep_id === self::DEPARTMENT_SALES;
    }

    /**
     * @return bool
     */
    public function isExchange(): bool
    {
        return $this->dep_id === self::DEPARTMENT_EXCHANGE;
    }

    /**
     * @return bool
     */
    public function isSupport(): bool
    {
        return $this->dep_id === self::DEPARTMENT_SUPPORT;
    }

    /**
     * @return bool
     */
    public function isScheduleChange(): bool
    {
        return $this->dep_id === self::DEPARTMENT_SCHEDULE_CHANGE;
    }

    public function isFraudPrevention(): bool
    {
        return $this->dep_id === self::DEPARTMENT_FRAUD_PREVENTION;
    }

    /**
     * @param int $depId
     * @return string
     */
    public static function getName(int $depId): string
    {
        return self::DEPARTMENT_LIST[$depId] ?? 'Undefined';
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'department';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dep_id', 'dep_name'], 'required'],
            [['dep_id', 'dep_updated_user_id'], 'integer'],
            [['dep_updated_dt'], 'safe'],
            [['dep_key', 'dep_name'], 'string', 'max' => 20],
            [['dep_id'], 'unique'],
            [['dep_key'], 'unique'],
            [['dep_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['dep_updated_user_id' => 'id']],

            ['dep_params', 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'dep_id' => 'ID',
            'dep_key' => 'Key',
            'dep_name' => 'Name',
            'dep_updated_user_id' => 'Updated User',
            'dep_updated_dt' => 'Updated Date',
            'dep_params' => 'Parameters',
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
                    ActiveRecord::EVENT_BEFORE_INSERT => ['dep_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['dep_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'dep_updated_user_id',
                'updatedByAttribute' => 'dep_updated_user_id',
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCalls()
    {
        return $this->hasMany(Call::class, ['c_dep_id' => 'dep_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDepartmentPhoneProjects()
    {
        return $this->hasMany(DepartmentPhoneProject::class, ['dpp_dep_id' => 'dep_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserDepartments()
    {
        return $this->hasMany(UserDepartment::class, ['ud_dep_id' => 'dep_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDepUpdatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'dep_updated_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLeads()
    {
        return $this->hasMany(Lead::class, ['l_dep_id' => 'dep_id']);
    }

    /**
     * {@inheritdoc}
     * @return DepartmentQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new DepartmentQuery(static::class);
    }

    /**
     * @return array
     */
    public static function getList(): array
    {
        $data = self::find()->orderBy(['dep_id' => SORT_ASC])->asArray()->all();
        return ArrayHelper::map($data, 'dep_id', 'dep_name');
    }

    public static function getListExcludingExchange(): array
    {
        $data = self::find()->where(['<>', 'dep_id', self::DEPARTMENT_EXCHANGE])->orderBy(['dep_id' => SORT_ASC])->asArray()->all();
        return ArrayHelper::map($data, 'dep_id', 'dep_name');
    }

    public function getParams(): ?Params
    {
        try {
            $data = Json::decode($this->dep_params);
            return new Params($data);
        } catch (\Throwable $e) {
            \Yii::error($e->getMessage(), 'Department:getParams');
            return null;
        }
    }

    /**
     * @return array
     */
    public static function getEnvListWOCache(): array
    {
        $data = self::find()->orderBy(['dep_id' => SORT_ASC])->asArray()->all();
        return ArrayHelper::map($data, 'dep_key', 'dep_name');
    }

    /**
     * @return array
     */
    public static function getEnvList(): array
    {
        //Yii::$app->cache->delete(self::CACHE_KEY);
        if (self::CACHE_KEY) {
            $list = Yii::$app->cache->get(self::CACHE_KEY);
            if ($list === false) {
                $list = self::getEnvListWOCache();

                Yii::$app->cache->set(
                    self::CACHE_KEY,
                    $list,
                    0,
                    new TagDependency(['tags' => self::CACHE_TAG_DEPENDENCY])
                );
            }
        } else {
            $list = self::getEnvListWOCache();
        }
        return $list;
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if (self::CACHE_TAG_DEPENDENCY) {
            TagDependency::invalidate(Yii::$app->cache, self::CACHE_TAG_DEPENDENCY);
        }
    }

    /**
     *
     */
    public function afterDelete(): void
    {
        parent::afterDelete();
        if (self::CACHE_TAG_DEPENDENCY) {
            TagDependency::invalidate(Yii::$app->cache, self::CACHE_TAG_DEPENDENCY);
        }
    }
}
