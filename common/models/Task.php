<?php

namespace common\models;

use common\models\query\TaskQuery;
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
 * @property int $t_category_id
 * @property int $t_sort_order
 *
 * @property LeadTask[] $leadTasks
 */
class Task extends \yii\db\ActiveRecord
{

    public const CAT_NOT_ANSWERED_PROCESS = 1;
    public const CAT_ANSWERED_PROCESS = 2;

    public const CAT_LIST = [
        self::CAT_NOT_ANSWERED_PROCESS  => 'NOT Answered process',
        self::CAT_ANSWERED_PROCESS      => 'Answered process (for Book)',
    ];


    public const TYPE_MISSED_CALL = 'missed-call';

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
            [['t_hidden', 't_category_id', 't_sort_order'], 'integer'],
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
            't_category_id' => 'Category',
            't_sort_order' => 'Sort Order'
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

    /**
     * @return mixed|string
     */
    public function getCategoryName()
    {
        return self::CAT_LIST[$this->t_category_id] ?? '-';
    }
}
