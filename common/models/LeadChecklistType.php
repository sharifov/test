<?php

namespace common\models;

use common\models\query\LeadChecklistTypeQuery;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "lead_checklist_type".
 *
 * @property int $lct_id
 * @property string $lct_key
 * @property string $lct_name
 * @property string $lct_description
 * @property int $lct_enabled
 * @property int $lct_sort_order
 * @property string $lct_updated_dt
 * @property int $lct_updated_user_id
 *
 * @property LeadChecklist[] $leadChecklists
 * @property Employee $lctUpdatedUser
 */
class LeadChecklistType extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lead_checklist_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lct_key', 'lct_name'], 'required'],
            [['lct_enabled', 'lct_sort_order', 'lct_updated_user_id'], 'integer'],
            [['lct_updated_dt'], 'safe'],
            [['lct_key'], 'string', 'max' => 50],
            [['lct_name'], 'string', 'max' => 255],
            [['lct_description'], 'string', 'max' => 500],
            [['lct_key'], 'unique'],
            [['lct_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['lct_updated_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'lct_id' => 'ID',
            'lct_key' => 'Key',
            'lct_name' => 'Name',
            'lct_description' => 'Description',
            'lct_enabled' => 'Enabled',
            'lct_sort_order' => 'Sort Order',
            'lct_updated_dt' => 'Updated Dt',
            'lct_updated_user_id' => 'Updated User',
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
                    ActiveRecord::EVENT_BEFORE_INSERT => ['lct_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['lct_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'lct_updated_user_id',
                'updatedByAttribute' => 'lct_updated_user_id',
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLeadChecklists()
    {
        return $this->hasMany(LeadChecklist::class, ['lc_type_id' => 'lct_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLctUpdatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'lct_updated_user_id']);
    }

    /**
     * {@inheritdoc}
     * @return LeadChecklistTypeQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new LeadChecklistTypeQuery(static::class);
    }


    /**
     * @param bool $enabled
     * @return array
     */
    public static function getList(bool $enabled = true) : array
    {
        $query = self::find()->orderBy(['lct_sort_order' => SORT_ASC]);
        if($enabled) {
            $query->andWhere(['lct_enabled' => true]);
        }
        $data = $query->asArray()->all();
        return ArrayHelper::map($data, 'lct_id', 'lct_name');
    }
}
