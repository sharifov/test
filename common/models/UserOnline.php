<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "user_online".
 *
 * @property int $uo_user_id
 * @property string|null $uo_updated_dt
 *
 * @property Employee $uoUser
 */
class UserOnline extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'user_online';
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['uo_updated_dt'], 'safe'],
            [['uo_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['uo_user_id' => 'id']],
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
                    ActiveRecord::EVENT_BEFORE_INSERT => ['uo_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['uo_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'uo_user_id' => 'User ID',
            'uo_updated_dt' => 'Updated Dt',
        ];
    }

    /**
     * Gets query for [[UoUser]].
     *
     * @return ActiveQuery
     */
    public function getUoUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'uo_user_id']);
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ($insert) {
            Yii::warning(VarDumper::dumpAsString($insert), 'info\UserOnline:afterSave:insert');
        } else {
            Yii::warning(VarDumper::dumpAsString($insert), 'info\UserOnline:afterSave:update');
        }
    }
}
