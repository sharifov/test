<?php

namespace src\model\dbDataSensitiveView\entity;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use src\model\dbDataSensitive\entity\DbDataSensitive;

/**
 * This is the model class for table "date_sensitive_view".
 *
 * @property int|null $ddv_dda_id
 * @property string|null $ddv_view_name
 * @property string|null $ddv_table_name
 * @property string|null $ddv_created_dt
 *
 * @property DbDataSensitive $dbDataSensitive
 */
class DbDataSensitiveView extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'db_data_sensitive_view';
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['ddv_created_dt'],
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
            [['ddv_dda_id'], 'integer'],
            [['ddv_created_dt'], 'safe'],
            [['ddv_view_name', 'ddv_table_name'], 'string', 'max' => 255],
            [['ddv_dda_id'], 'exist', 'skipOnError' => true, 'targetClass' => DbDataSensitive::className(), 'targetAttribute' => ['ddv_dda_id' => 'dda_id']],
        ];
    }

    /**
     * @param int $ddv_dda_id
     * @param string $ddv_view_name
     * @param string $ddv_table_name
     * @return DbDataSensitiveView
     */
    public static function create(int $ddv_dda_id, string $ddv_view_name, string $ddv_table_name): DbDataSensitiveView
    {
        $model = new self();
        $model->ddv_dda_id = $ddv_dda_id;
        $model->ddv_view_name = $ddv_view_name;
        $model->ddv_table_name = $ddv_table_name;
        return $model;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ddv_da_id' => 'Dv Da ID',
            'ddv_view_name' => 'Dv View Name',
            'ddv_table_name' => 'Dv Table Name',
            'ddv_created_dt' => 'Dv Created Dt',
        ];
    }

    /**
     * Gets query for [[DvDa]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDbDateSensitive()
    {
        return $this->hasOne(DbDataSensitive::className(), ['dda_id' => 'ddv_dda_id']);
    }
}
