<?php

namespace modules\requestControl\models;

/**
 * Class Rule
 *
 * @property integer $rcr_id
 * @property string $rcr_type
 * @property string $rcr_subject
 * @property integer $rcr_local
 * @property integer $rcr_global
 *
 */
class RequestControlRule extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @return string
     */
    public static function tableName(): string
    {
        return 'request_control_rule';
    }

    /**
     * @inheritdoc
     * @return array
     */
    public function rules(): array
    {
        return [
            [['rcr_type', 'rcr_subject', 'rcr_local', 'rcr_global'], 'required'],
            ['rcr_type', 'string', 'max' => 50],
            ['rcr_subject', 'string', 'max' => 255],
            ['rcr_local', 'integer'],
            ['rcr_global', 'integer'],
            [['rcr_local', 'rcr_global'], 'default', 'value' => 0]
        ];
    }

    /**
     * @inheritdoc
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
          'rcr_type' => 'Rule type',
          'rcr_subject' => 'Rule subject',
          'rcr_local' => 'Local limit',
          'rcr_global' => 'Global limit'
        ];
    }
}
