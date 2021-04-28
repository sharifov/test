<?php

namespace modules\abac\src\entities;

use common\models\Employee;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

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
            [['ap_object', 'ap_action', 'ap_title'], 'string', 'max' => 255],
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
                    ActiveRecord::EVENT_BEFORE_INSERT => ['ap_created_dt', 'ap_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['ap_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'ap_created_user_id',
                'updatedByAttribute' => 'ap_updated_user_id',
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
        ];
    }

    /**
     * Gets query for [[ApCreatedUser]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getApCreatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'ap_created_user_id']);
    }

    /**
     * Gets query for [[ApUpdatedUser]].
     *
     * @return \yii\db\ActiveQuery
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
     * @return array
     */
    public function getActionList(): array
    {
        $list = [];
        $list['view'] = 'view';
        $list['edit'] = 'edit';
        $list['delete'] = 'delete';
        return $list;
    }

    public function getObjectList(): array
    {
        //$list = [];
        $list = Yii::$app->abac->getObjectList();
        //$list['hotel'] = 'hotel/*';
        //$list['flight'] = 'flight/*';
        return $list;
    }


}
