<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "task".
 *
 * @property int $t_id
 * @property string $t_key
 * @property string $t_name
 * @property string $t_description
 * @property int $t_hidden
 *
 * @property LeadTask[] $leadTasks
 */
class Task extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'task';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['t_key', 't_name'], 'required'],
            [['t_hidden'], 'integer'],
            [['t_key', 't_name'], 'string', 'max' => 100],
            [['t_description'], 'string', 'max' => 500],
            [['t_key'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            't_id' => 'ID',
            't_key' => 'Key',
            't_name' => 'Name',
            't_description' => 'Description',
            't_hidden' => 'Hidden',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLeadTasks()
    {
        return $this->hasMany(LeadTask::class, ['lt_task_id' => 't_id']);
    }

    /**
     * {@inheritdoc}
     * @return TaskQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TaskQuery(get_called_class());
    }

    /**
     * @return array
     */
    public static function getList() : array
    {
        $data = self::find()->orderBy(['t_id' => SORT_ASC])->asArray()->all();
        return ArrayHelper::map($data,'t_id', 't_name');
    }
}
