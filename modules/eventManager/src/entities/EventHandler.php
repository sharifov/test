<?php

namespace modules\eventManager\src\entities;

use common\components\validators\CheckJsonValidator;
use common\components\validators\CronExpressionValidator;
use common\models\Employee;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "event_handler".
 *
 * @property int $eh_id
 * @property int $eh_el_id
 * @property string $eh_class
 * @property string $eh_method
 * @property int $eh_enable_type
 * @property bool $eh_enable_log
 * @property bool $eh_asynch
 * @property bool $eh_break
 * @property int $eh_sort_order
 * @property string|null $eh_cron_expression
 * @property string|null $eh_condition
 * @property string|null $eh_params
 * @property string|null $eh_builder_json
 * @property string|null $eh_updated_dt
 * @property int|null $eh_updated_user_id
 *
 * @property EventList $eventList
 * @property Employee $updatedUser
 */
class EventHandler extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName()
    {
        return 'event_handler';
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['eh_el_id', 'eh_enable_type', 'eh_enable_log', 'eh_asynch', 'eh_break', 'eh_sort_order',
                'eh_updated_user_id'], 'integer'],
            [['eh_class', 'eh_method', 'eh_el_id'], 'required'],

            ['eh_enable_type', 'in', 'range' => array_keys(EventList::getEnableTypeList())],

            [['eh_condition', 'eh_params'], 'string'],
            [['eh_params'], CheckJsonValidator::class],

            [['eh_params'], 'filter', 'filter' => function ($value) {
                try {
                    $data = [];
                    if (is_string($value)) {
                        $data = \yii\helpers\Json::decode($value);
                    }
                    return $data;
                } catch (\Throwable $throwable) {
                    $this->addError('eh_params', $throwable->getMessage());
                    return null;
                }
            }],

            [['eh_builder_json', 'eh_updated_dt'], 'safe'],
            [['eh_class'], 'string', 'max' => 500],
            [['eh_method', 'eh_cron_expression'], 'string', 'max' => 255],
            [['eh_el_id'], 'exist', 'skipOnError' => true, 'targetClass' => EventList::class,
                'targetAttribute' => ['eh_el_id' => 'el_id']],
            [['eh_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class,
                'targetAttribute' => ['eh_updated_user_id' => 'id']],

            ['eh_cron_expression', CronExpressionValidator::class, 'skipOnEmpty' => true, 'skipOnError' => true],
        ];
    }

    public function validateJson($attribute, $params, $validator)
    {
        // The JSON might already be a string
        if (is_string($this->$attribute)) {
            $decoded = \yii\helpers\Json::decode($this->$attribute);
        }
        // Now perform your custom validation
        // In case of error add the error to the field
        // $this->addError( $attribute, 'My custom error' );
        // This will validate the model to false
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
                    BaseActiveRecord::EVENT_BEFORE_INSERT => ['eh_updated_dt'],
                    BaseActiveRecord::EVENT_BEFORE_UPDATE => ['eh_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'eh_updated_user_id',
                'updatedByAttribute' => 'eh_updated_user_id',
            ],
        ];
    }

    /**
     * @return string[]
     */
    public function attributeLabels()
    {
        return [
            'eh_id' => 'ID',
            'eh_el_id' => 'El ID',
            'eh_class' => 'Class',
            'eh_method' => 'Method',
            'eh_enable_type' => 'Enable Type',
            'eh_enable_log' => 'Enable Log',
            'eh_asynch' => 'Asynch',
            'eh_break' => 'Break',
            'eh_sort_order' => 'Sort Order',
            'eh_cron_expression' => 'Cron Expression',
            'eh_condition' => 'Condition',
            'eh_params' => 'Params',
            'eh_builder_json' => 'Builder Json',
            'eh_updated_dt' => 'Updated Dt',
            'eh_updated_user_id' => 'Updated User ID',
        ];
    }


    /**
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        Yii::$app->event->invalidateCache();
    }

    /**
     * @return bool
     */
    public function beforeDelete(): bool
    {
        if (!parent::beforeDelete()) {
            return false;
        }

        Yii::$app->event->invalidateCache();
        return true;
    }

    /**
     * Gets query for [[EventList]].
     *
     * @return ActiveQuery
     */
    public function getEventList(): ActiveQuery
    {
        return $this->hasOne(EventList::class, ['el_id' => 'eh_el_id']);
    }

    /**
     * Gets query for [[UpdatedUser]].
     *
     * @return ActiveQuery
     */
    public function getUpdatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'eh_updated_user_id']);
    }

    /**
     * {@inheritdoc}
     * @return EventHandlerScopes the active query used by this AR class.
     */
    public static function find(): EventHandlerScopes
    {
        return new EventHandlerScopes(get_called_class());
    }
}
