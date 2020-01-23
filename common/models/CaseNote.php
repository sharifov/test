<?php

namespace common\models;

use common\models\query\CaseNoteQuery;
use sales\entities\cases\Cases;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "case_note".
 *
 * @property int $cn_id
 * @property int $cn_cs_id
 * @property int $cn_user_id
 * @property string $cn_text
 * @property string $cn_created_dt
 * @property string $cn_updated_dt
 *
 * @property Cases $cnCs
 * @property Employee $cnUser
 */
class CaseNote extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'case_note';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cn_cs_id', 'cn_text'], 'required'],
            [['cn_cs_id', 'cn_user_id'], 'integer'],
            [['cn_text'], 'string'],
            [['cn_created_dt', 'cn_updated_dt'], 'safe'],
            [['cn_cs_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cases::class, 'targetAttribute' => ['cn_cs_id' => 'cs_id']],
            [['cn_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['cn_user_id' => 'id']],
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
                    ActiveRecord::EVENT_BEFORE_INSERT => ['cn_created_dt', 'cn_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['cn_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'cn_user_id',
                'updatedByAttribute' => 'cn_user_id',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'cn_id' => 'ID',
            'cn_cs_id' => 'Case ID',
            'cn_user_id' => 'User ID',
            'cn_text' => 'Text',
            'cn_created_dt' => 'Created Dt',
            'cn_updated_dt' => 'Updated Dt',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCnCs()
    {
        return $this->hasOne(Cases::class, ['cs_id' => 'cn_cs_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCnUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'cn_user_id']);
    }

    /**
     * {@inheritdoc}
     * @return CaseNoteQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CaseNoteQuery(static::class);
    }
}
