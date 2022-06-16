<?php

namespace modules\abac\src\entities;

use BaconQrCode\Renderer\Text\Html;
use common\models\Employee;
use modules\abac\src\AbacService;
use src\behaviors\cache\CleanCacheBehavior;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\caching\TagDependency;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;
use yii\helpers\Json;

/**
 * This is the model class for table "abac_policy".
 *
 * @property int $ap_id
 * @property string $ap_rule_type
 * @property string|null $ap_subject
 * @property string|null $ap_subject_json
 * @property string $ap_object
 * @property string|null $ap_action
 * @property string|null $ap_action_json
 * @property int $ap_effect
 * @property string|null $ap_title
 * @property int|null $ap_sort_order
 * @property string|null $ap_created_dt
 * @property string|null $ap_updated_dt
 * @property int|null $ap_created_user_id
 * @property int|null $ap_updated_user_id
 * @property bool|null $ap_enabled
 * @property string|null $ap_hash_code
 *
 * @property Employee $apCreatedUser
 * @property Employee $apUpdatedUser
 */
class AbacPolicy extends ActiveRecord
{
    public const EFFECT_DENY    = 0;
    public const EFFECT_ALLOW   = 1;

    public const EFFECT_LIST = [
        self::EFFECT_DENY => 'deny',
        self::EFFECT_ALLOW => 'allow',
    ];

    public const CACHE_KEY = 'abac-policy-model';

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'abac_policy';
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['ap_subject_json', 'ap_action_json', 'ap_created_dt', 'ap_updated_dt'], 'safe'],
            [['ap_object'], 'required'],
            [['ap_effect', 'ap_sort_order', 'ap_created_user_id', 'ap_updated_user_id'], 'integer'],
            [['ap_rule_type'], 'string', 'max' => 2],
            [['ap_subject'], 'string', 'max' => 10000],
            [['ap_object', 'ap_title'], 'string', 'max' => 255],
            [['ap_hash_code'], 'string', 'max' => 32],
            [['ap_action'], 'string', 'max' => 1000],
            [['ap_enabled'], 'boolean'],
            [['ap_created_user_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Employee::class, 'targetAttribute' => ['ap_created_user_id' => 'id']],
            [['ap_updated_user_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Employee::class, 'targetAttribute' => ['ap_updated_user_id' => 'id']],
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
                    BaseActiveRecord::EVENT_BEFORE_INSERT => ['ap_created_dt', 'ap_updated_dt'],
                    BaseActiveRecord::EVENT_BEFORE_UPDATE => ['ap_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'ap_created_user_id',
                'updatedByAttribute' => 'ap_updated_user_id',
            ],
            'cleanCache' => [
                'class' => CleanCacheBehavior::class,
                'tags' => [self::CACHE_KEY . $this->ap_object],
            ],
        ];
    }

    /**
     * @return string[]
     */
    public function attributeLabels()
    {
        return [
            'ap_id' => 'ID',
            'ap_rule_type' => 'Rule Type',
            'ap_subject' => 'Subject',
            'ap_subject_json' => 'Subject Json',
            'ap_object' => 'Object',
            'ap_action' => 'Action',
            'ap_action_json' => 'Action Json',
            'ap_effect' => 'Effect',
            'ap_title' => 'Title',
            'ap_sort_order' => 'Sort Order',
            'ap_created_dt' => 'Created Dt',
            'ap_updated_dt' => 'Updated Dt',
            'ap_created_user_id' => 'Created User ID',
            'ap_updated_user_id' => 'Updated User ID',
            'ap_enabled' => 'Enabled',
            'ap_hash_code' => 'Hash Code',
        ];
    }

    public function beforeSave($insert): bool
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if ($this->ap_action_json) {
            $this->ap_action = $this->getActionListById();
        }

        if ($this->ap_subject_json) {
            $this->ap_subject = $this->getDecodeCode();
        }
        $this->ap_hash_code = $this->generateHashCode();
        return true;
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        $cacheTagDependency = Yii::$app->abac->getCacheTagDependency();
        if ($cacheTagDependency) {
            TagDependency::invalidate(Yii::$app->cache, $cacheTagDependency);
        }
    }

    /**
     * @return bool
     */
    public function beforeDelete(): bool
    {
        if (!parent::beforeDelete()) {
            return false;
        }

        $cacheTagDependency = Yii::$app->abac->getCacheTagDependency();
        if ($cacheTagDependency) {
            TagDependency::invalidate(Yii::$app->cache, $cacheTagDependency);
        }
        return true;
    }

    /**
     * @return string
     */
    public function getActionListById(): string
    {
        $str = '';
        if ($this->ap_action_json) {
            $actionData = @json_decode($this->ap_action_json, true);
            if ($actionData && is_array($actionData)) {
                $values = [];
                foreach ($actionData as $actionId) {
                    $values[] = '(' . $actionId . ')';
                }
                $str = implode('|', $values);
            }
        }
        return $str;
    }

    /**
     * @return string
     */
    public function getSubjectByJson(): string
    {
        $str = '';
        if ($this->ap_subject_json) {
            //$data = @json_decode($this->ap_subject_json);
        }
        return $str;
    }

    /**
     * Gets query for [[ApCreatedUser]].
     *
     * @return ActiveQuery
     */
    public function getApCreatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'ap_created_user_id']);
    }

    /**
     * Gets query for [[ApUpdatedUser]].
     *
     * @return ActiveQuery
     */
    public function getApUpdatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'ap_updated_user_id']);
    }

    /**
     * @return string[]
     */
    public static function getEffectList(): array
    {
        return self::EFFECT_LIST;
    }

    /**
     * @return string
     */
    public function getEffectName(): string
    {
        return self::EFFECT_LIST[$this->ap_effect] ?? '-';
    }

    /**
     * @return string
     */
    public function getEffectLabel(): string
    {
        $name = $this->getEffectName();
        $class = $this->ap_effect === self::EFFECT_ALLOW ? 'success' : 'danger';
        return $name ? '<span class="badge badge-' . $class . '">' . \yii\helpers\Html::encode($name) . '</span>' :  '-';
    }

    /**
     * @param bool $human
     * @return string
     */
    public function getDecodeCode(bool $human = false): string
    {
        $code = '';
        $rules = @json_decode($this->ap_subject_json, true);
        if (is_array($rules)) {
            $code = AbacService::conditionDecode($rules);

            if ($human) {
                $code = AbacService::humanConditionCode($code);
            }
        }
        return $code;
    }

    /**
     * @return array
     */
    public function getActionList(): array
    {
        $dataList = [];
        if (!empty($this->ap_action)) {
            $actionList = explode('|', $this->ap_action);
            if ($actionList) {
                foreach ($actionList as $item) {
                    $dataList[] = str_replace(['(', ')'], '', $item);
                }
            }
        }
        return $dataList;
    }

    /**
     * @return string
     */
    public function generateHashCode(): string
    {
        $data[] = $this->ap_object;
        $data[] = $this->ap_action;
        $data[] = $this->ap_subject;
        $data[] = $this->ap_effect;
        return AbacService::generateHashCode($data);
    }

    /**
     * @return array
     */
    public static function getDuplicateListId(): array
    {
        return self::find()->select(['ap_hash_code'])
            ->groupBy(['ap_hash_code'])
            ->having('COUNT(*) > 1')->column();
    }

    /**
     * @return string
     */
    public function getDump(): string
    {
        return base64_encode(Json::encode($this->attributes));
    }
}
