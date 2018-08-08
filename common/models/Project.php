<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "projects".
 *
 * @property int $id
 * @property string $name
 * @property string $link
 * @property string $api_key
 * @property string $contact_info
 * @property int $closed
 * @property string $last_update
 *
 * @property Source[] $sources
 */
class Project extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'projects';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['contact_info'], 'string'],
            [['closed'], 'integer'],
            [['last_update'], 'safe'],
            [['name', 'link', 'api_key'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'link' => 'Link',
            'api_key' => 'Api Key',
            'contact_info' => 'Contact Info',
            'closed' => 'Closed',
            'last_update' => 'Last Update',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSources()
    {
        return $this->hasMany(Source::className(), ['project_id' => 'id']);
    }
}
