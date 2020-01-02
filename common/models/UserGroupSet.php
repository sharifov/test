<?php

namespace common\models;

use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_group_set".
 *
 * @property int $ugs_id
 * @property string|null $ugs_name
 * @property int|null $ugs_enabled
 * @property string|null $ugs_created_dt
 * @property string|null $ugs_updated_dt
 * @property int|null $ugs_updated_user_id
 *
 * @property UserGroup[] $userGroups
 * @property Employee $updatedUser
 */
class UserGroupSet extends \yii\db\ActiveRecord
{

    public static function tableName(): string
    {
        return 'user_group_set';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['ugs_name', 'required'],
            ['ugs_name', 'string', 'max' => 255],

            [['ugs_enabled', 'ugs_updated_user_id'], 'integer'],

            ['ugs_updated_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['ugs_updated_user_id' => 'id']],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'ugs_id' => 'ID',
            'ugs_name' => 'Name',
            'ugs_enabled' => 'Enabled',
            'ugs_created_dt' => 'Created Dt',
            'ugs_updated_dt' => 'Updated Dt',
            'ugs_updated_user_id' => 'Updated User',
        ];
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['ugs_created_dt', 'ugs_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['ugs_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'ugs_updated_user_id',
                'updatedByAttribute' => 'ugs_updated_user_id',
            ],
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getUserGroups(): ActiveQuery
    {
        return $this->hasMany(UserGroup::class, ['ug_user_group_set_id' => 'ugs_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUpdatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'ugs_updated_user_id']);
    }

    /**
     * @return array
     */
    public static function getList(): array
    {
        return self::find()->select(['ugs_name', 'ugs_id'])->orderBy(['ugs_name' => SORT_ASC])->indexBy('ugs_id')->asArray()->column();
    }

    /**
     * @return UserGroupSetQuery
     */
    public static function find(): UserGroupSetQuery
    {
        return new UserGroupSetQuery(static::class);
    }
}
