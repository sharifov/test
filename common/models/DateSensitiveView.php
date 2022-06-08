<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "date_sensitive_view".
 *
 * @property int|null $dv_da_id
 * @property string|null $dv_view_name
 * @property string|null $dv_table_name
 * @property string|null $dv_created_dt
 *
 * @property DateSensitive $dateSensitive
 */
class DateSensitiveView extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'date_sensitive_view';
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['dv_created_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dv_da_id'], 'integer'],
            [['dv_created_dt'], 'safe'],
            [['dv_view_name', 'dv_table_name'], 'string', 'max' => 255],
            [['dv_da_id'], 'exist', 'skipOnError' => true, 'targetClass' => DateSensitive::className(), 'targetAttribute' => ['dv_da_id' => 'da_id']],
        ];
    }

    /**
     * @param int $dv_da_id
     * @param string $dv_view_name
     * @param string $dv_table_name
     * @return DateSensitiveView
     */
    public static function create(int $dv_da_id, string $dv_view_name, string $dv_table_name): DateSensitiveView
    {
        $model = new self();
        $model->dv_da_id = $dv_da_id;
        $model->dv_view_name = $dv_view_name;
        $model->dv_table_name = $dv_table_name;
        return $model;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'dv_da_id' => 'Dv Da ID',
            'dv_view_name' => 'Dv View Name',
            'dv_table_name' => 'Dv Table Name',
            'dv_created_dt' => 'Dv Created Dt',
        ];
    }

    /**
     * Gets query for [[DvDa]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDateSensitive()
    {
        return $this->hasOne(DateSensitive::className(), ['da_id' => 'dv_da_id']);
    }
}
