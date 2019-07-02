<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "log".
 *
 * @property integer $id
 * @property integer $level
 * @property string $category
 * @property double $log_time
 * @property string $prefix
 * @property string $message
 */
class Log extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['level', 'id'], 'integer'],
            [['log_time'], 'number'],
            [['prefix', 'message'], 'string'],
            [['category'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'level' => 'Level',
            'category' => 'Category',
            'log_time' => 'Log Time',
            'prefix' => 'Prefix',
            'message' => 'Message',
        ];
    }

    /**
     * @inheritdoc
     * @return LogQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new LogQuery(get_called_class());
    }

    /**
     * @return array
     */
    public static function getCategoryFilter()
    {
        $arr = [];
        $data = self::find()->select(["COUNT(*) AS cnt", "category"])
            ->where('category IS NOT NULL')
            //->andWhere("job_start_dt >= NOW() - interval '24 hour'")
            ->groupBy(["category"])
            ->orderBy('cnt DESC')->asArray()->all();

        if($data)
            foreach ($data as $v) {
                $arr[$v['category']] = $v['category'].' - ['.$v['cnt'].']';
            }

        return $arr;
    }

    /**
     * @param $model Log
     * @return int|string
     */
    public static function removeSysLogs($model){
        $beforeDelete = self::find()->count();

        if ($model->level != '' && $model->category == '' && $model->days == ''){
            self::deleteAll('level = :l', [':l' => $model->level]);
        }
        if ($model->level == '' && $model->category != '' && $model->days == ''){
            self::deleteAll('category = :c', [':c' => $model->category]);
        }
        if ($model->level == '' && $model->category == '' && $model->days != ''){
            self::deleteAll('log_time <= :d', [':d' => strtotime('-'.$model->days.' day')]);
        }
        if ($model->level != '' && $model->category != '' && $model->days == ''){
            self::deleteAll(['AND','level = :l', 'category = :c'], [':l' => $model->level, ':c' => $model->category]);
        }
        if ($model->level != '' && $model->category == '' && $model->days != ''){
            self::deleteAll(['AND','level = :l', 'log_time <= :d'], [':l' => $model->level, ':d' => strtotime('-'.$model->days.' day')]);
        }
        if ($model->level == '' && $model->category != '' && $model->days != ''){
            self::deleteAll(['AND','category = :c', 'log_time <= :d'], [':c' => $model->category, ':d' => strtotime('-'.$model->days.' day')]);
        }
        if ($model->level != '' && $model->category != '' && $model->days != ''){
            self::deleteAll('level = :l AND category = :c AND log_time <= :d', [':l' => $model->level, ':c' => $model->category, ':d' => strtotime('-'.$model->days.' day')]);
        }
        $afterDelete = self::find()->count();

        return $beforeDelete - $afterDelete;
    }
}
