<?php
/**
 * User: shakarim
 * Date: 3/31/22
 * Time: 7:30 PM
 */

namespace modules\requestControl\models;

/**
 * Class Rule
 *
 * @property integer $id
 * @property string $type
 * @property string $subject
 * @property integer $local
 * @property integer $global
 *
 */
class Rule extends \yii\db\ActiveRecord
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
            [['type', 'subject', 'local', 'global'], 'required'],
            ['type', 'string', 'max' => 50],
            ['subject', 'string', 'max' => 255],
            ['local', 'integer'],
            ['global', 'integer'],
            [['local', 'global'], 'default', 'value' => 0]
        ];
    }

    /**
     * @inheritdoc
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
          'type' => 'Rule type',
          'subject' => 'Rule subject',
          'local' => 'Local limit',
          'global' => 'Global limit'
        ];
    }
}