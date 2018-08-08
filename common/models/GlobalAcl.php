<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "global_acl".
 *
 * @property int $id
 * @property string $mask
 * @property int $active
 * @property string $created
 * @property string $updated
 * @property string $description
 */
class GlobalAcl extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'global_acl';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['mask', 'description'], 'required'],
            [['active'], 'integer'],
            [['created', 'updated'], 'safe'],
            ['mask', 'unique'],
            ['mask', 'ip', 'ipv6' => false],
            [['description'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'mask' => 'IP',
            'active' => 'Active',
            'created' => 'Created',
            'updated' => 'Updated',
            'description' => 'Description'
        ];
    }

    public function afterValidate()
    {
        parent::afterValidate();
        $this->updated = date('Y-m-d H:i:s');
    }

    public static function isActiveIPRule($ip)
    {
        return self::findOne(['active' => true, 'mask' => trim($ip)]);
    }
}
