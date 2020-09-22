<?php

namespace sales\model\call\entity\callCommand;

use common\models\Employee;
use common\models\Project;
use sales\behaviors\StringToJsonBehavior;
use sales\model\call\entity\callCommand\behaviors\RefreshCommandLineJsonBehavior;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

use function Amp\Promise\timeoutWithDefault;

/**
 * This is the model class for table "call_command".
 *
 * @property int $ccom_id
 * @property int|null $ccom_parent_id
 * @property int|null $ccom_project_id
 * @property string|null $ccom_lang_id
 * @property string|null $ccom_name
 * @property int $ccom_type_id
 * @property string|null $ccom_params_json
 * @property int|null $ccom_sort_order
 * @property int|null $ccom_user_id
 * @property int|null $ccom_created_user_id
 * @property int|null $ccom_updated_user_id
 * @property string|null $ccom_created_dt
 * @property string|null $ccom_updated_dt
 *
 * @property CallCommand[] $callCommands
 * @property Employee $ccomCreatedUser
 * @property CallCommand $ccomParent
 * @property Project $ccomProject
 * @property Employee $ccomUpdatedUser
 * @property Employee $ccomUser
 */
class CallCommand extends \yii\db\ActiveRecord
{

    public const TYPE_SAY           = 1;
    public const TYPE_PLAY          = 2;
    public const TYPE_PAUSE         = 3;
    public const TYPE_REJECT        = 4;
    public const TYPE_REFER         = 5;
    public const TYPE_HANGUP        = 6;
    public const TYPE_GATHER        = 7;
    public const TYPE_DIAL          = 8;
    public const TYPE_REDIRECT      = 9;
    public const TYPE_TWIML         = 10;
    public const TYPE_COMMAND_LIST  = 11;
    public const TYPE_FORWARD       = 12;
    public const TYPE_VOICE_MAIL    = 13;


    public const TYPE_LIST = [
        self::TYPE_SAY              => 'Say',
        self::TYPE_PLAY             => 'Play',
        self::TYPE_PAUSE            => 'Pause',
        self::TYPE_REJECT           => 'Reject',
        self::TYPE_REFER            => 'Refer',
        self::TYPE_HANGUP           => 'Hangup',
        self::TYPE_GATHER           => 'Gather',
        self::TYPE_DIAL             => 'Dial',
        self::TYPE_REDIRECT         => 'Redirect',
        self::TYPE_TWIML            => 'TwiML',
        self::TYPE_COMMAND_LIST     => 'Command List',
        self::TYPE_FORWARD          => 'Forward',
        self::TYPE_VOICE_MAIL       => 'Voice Mail',
    ];


    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'call_command';
    }

    /**
     * @return array|array[]
     */
    public function rules()
    {
        return [
            [['ccom_parent_id', 'ccom_project_id', 'ccom_type_id', 'ccom_sort_order', 'ccom_user_id', 'ccom_created_user_id', 'ccom_updated_user_id'], 'integer'],
            [['ccom_type_id'], 'required'],
            [['ccom_params_json', 'ccom_created_dt', 'ccom_updated_dt'], 'safe'],
            [['ccom_lang_id'], 'string', 'max' => 5],
            [['ccom_name'], 'string', 'max' => 100],
            [['ccom_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['ccom_created_user_id' => 'id']],
            [['ccom_parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => CallCommand::class, 'targetAttribute' => ['ccom_parent_id' => 'ccom_id']],
            [['ccom_project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['ccom_project_id' => 'id']],
            [['ccom_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['ccom_user_id' => 'id']],
            [['ccom_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['ccom_updated_user_id' => 'id']],

            ['ccom_sort_order', 'required', 'when' => function ($model) {
                return !empty($model->ccom_parent_id);
            }, 'whenClient' => "function (attribute, value) {
                return $('#callCommandParent').val().length;
            }"],

            [['ccom_sort_order'], 'unique', 'targetAttribute' => ['ccom_parent_id', 'ccom_sort_order'],
                'message' => 'This parent already has a child with this sorting',
                    'when' => function($model) {
                        return (!empty($model->ccom_parent_id) && !empty($model->ccom_sort_order));
                    }, 'enableClientValidation' => false],
        ];
    }

    /**
     * @return array|string[]
     */
    public function attributeLabels()
    {
        return [
            'ccom_id' => 'ID',
            'ccom_parent_id' => 'Parent ID',
            'ccom_project_id' => 'Project ID',
            'ccom_lang_id' => 'Language ID',
            'ccom_name' => 'Name',
            'ccom_type_id' => 'Type ID',
            'ccom_params_json' => 'Params Json',
            'ccom_sort_order' => 'Sort Order',
            'ccom_user_id' => 'User ID',
            'ccom_created_user_id' => 'Created User ID',
            'ccom_updated_user_id' => 'Updated User ID',
            'ccom_created_dt' => 'Created Dt',
            'ccom_updated_dt' => 'Updated Dt',
        ];
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['ccom_created_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['ccom_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['ccom_created_user_id'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['ccom_updated_user_id'],
                ]
            ],
            'stringToJson' => [
                'class' => StringToJsonBehavior::class,
                'jsonColumn' => 'ccom_params_json',
            ],
            'refreshJson' => [
                'class' => RefreshCommandLineJsonBehavior::class,
            ],
        ];
    }

    /**
     * Gets query for [[CallCommands]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCallCommands(): \yii\db\ActiveQuery
    {
        return $this->hasMany(CallCommand::class, ['ccom_parent_id' => 'ccom_id'])->orderBy(['ccom_sort_order' => SORT_ASC]);
    }

    /**
     * Gets query for [[CcomCreatedUser]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCcomCreatedUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'ccom_created_user_id']);
    }

    /**
     * Gets query for [[CcomParent]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCcomParent(): \yii\db\ActiveQuery
    {
        return $this->hasOne(CallCommand::class, ['ccom_id' => 'ccom_parent_id']);
    }

    /**
     * Gets query for [[CcomProject]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCcomProject(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Project::class, ['id' => 'ccom_project_id']);
    }

    /**
     * Gets query for [[CcomUpdatedUser]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCcomUpdatedUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'ccom_updated_user_id']);
    }

    /**
     * Gets query for [[CcomUser]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCcomUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'ccom_user_id']);
    }

    /**
     * @return string[]
     */
    public static function getTypeList(): array
    {
        return self::TYPE_LIST;
    }

    public static function getTypeName(int $typeId): ?string
    {
        return self::TYPE_LIST[$typeId] ?? null;
    }


    /**
     * @param bool $extended
     * @param int|null $typeId
     * @return array
     */
    public static function getList(bool $extended = false, ?int $typeId = null): array
    {
        $query = self::find()->select(['ccom_name', 'ccom_id', 'ccom_type_id'])->orderBy(['ccom_name' => SORT_ASC]);

        if ($typeId) {
            $query->where(['ccom_type_id' => $typeId]);
        }

        if ($extended) {
            $data = [];
            $list = $query->all();
            if ($list) {
                foreach ($list as $item) {
                    $data[$item->ccom_id] = self::getTypeName($item->ccom_type_id) . ($item->ccom_name ? ' [' . $item->ccom_name . ']' : '') . ' (id: ' . $item->ccom_id  . ' )';
                }
            }
        } else {
            $data = $query->indexBy('ccom_id')->asArray()->column();
        }

        return $data;
    }
}
