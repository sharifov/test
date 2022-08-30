<?php

namespace common\models;

use src\behaviors\DbDataSensitiveBehavior;
use src\behaviors\StringToJsonBehavior;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\Inflector;

/**
 * This is the model class for table "date_sensitive".
 *
 * @property int $dda_id
 * @property string $dda_key
 * @property string|null $dda_name
 * @property string|null $dda_source
 * @property string|null $dda_created_dt
 * @property string|null $dda_updated_dt
 * @property int|null $dda_created_user_id
 * @property int|null $dda_updated_user_id
 *
 * @property Employee $createdUser
 * @property Employee $updatedUser
 * @property DbDataSensitiveView[] $dbDataSensitiveViews
 */
class DbDataSensitive extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'db_data_sensitive';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dda_key', 'dda_name'], 'required'],
            [['dda_source', 'dda_created_dt', 'dda_updated_dt'], 'safe'],
            [['dda_created_user_id', 'dda_updated_user_id'], 'integer'],
            [['dda_key', 'dda_name'], 'string', 'max' => 50],
            [['dda_key'], 'unique'],
            [['dda_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::className(), 'targetAttribute' => ['dda_created_user_id' => 'id']],
            [['dda_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::className(), 'targetAttribute' => ['dda_updated_user_id' => 'id']],
            ['dda_key', 'filter', 'filter' => static function ($value) {
                return Inflector::slug($value, '_');
            }],
            ['dda_source', 'validateSource'],
        ];
    }


    public function validateSource($attribute, $params)
    {
        $tables = json_decode($this->{$attribute}, true);

        if (count($tables) === 0) {
            $this->addError($attribute, 'Source cannot be blank.');
        }

        foreach ($tables as $tableName => $fields) {
            $schema = Yii::$app->db->schema->getTableSchema($tableName);
            if (!$schema) {
                $this->addError($attribute, $tableName . " table not doesn't exist.");
            }

            if (!is_array($fields)) {
                $this->addError($attribute, $tableName . " table columns must be array");
            } else {
                foreach ($fields as $field) {
                    if (is_array($field) && !isset($schema->columns[$field['column']])) {
                        $this->addError($attribute, $tableName . " table doesn't have " . $field . " field.");
                    }
                }
            }
        }
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['dda_created_dt', 'dda_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['dda_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'dda_created_user_id',
                'updatedByAttribute' => 'dda_updated_user_id',
            ],
            'views' => [
                'class' => DbDataSensitiveBehavior::class,
            ],
            'stringToJson' => [
                'class' => StringToJsonBehavior::class,
                'jsonColumn' => 'dda_source'
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'dda_id' => 'Da ID',
            'dda_key' => 'Key',
            'dda_name' => 'Name',
            'dda_source' => 'Source',
            'dda_created_dt' => 'Created Date',
            'dda_updated_dt' => 'Updated Date',
            'dda_created_user_id' => 'Created User',
            'dda_updated_user_id' => 'Updated User',
        ];
    }

    /**
     * Gets query for [[DaCreatedUser]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedUser()
    {
        return $this->hasOne(Employee::className(), ['id' => 'dda_created_user_id']);
    }


    /**
     * Gets query for [[DbDateSensitiveViews]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDbDataSensitiveViews()
    {
        return $this->hasMany(DbDataSensitiveView::className(), ['ddv_dda_id' => 'dda_id']);
    }

    /**
     * Gets query for [[DaUpdatedUser]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedUser()
    {
        return $this->hasOne(Employee::className(), ['id' => 'dda_updated_user_id']);
    }
}
