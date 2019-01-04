<?php

namespace common\models;

use common\models\local\LeadLogMessage;
use Yii;

/**
 * This is the model class for table "clients".
 *
 * @property int $id
 * @property string $first_name
 * @property string $middle_name
 * @property string $last_name
 * @property string $created
 * @property string $updated
 *
 * @property string $full_name
 *
 * @property ClientEmail[] $clientEmails
 * @property ClientPhone[] $clientPhones
 * @property Lead[] $leads
 */
class Client extends \yii\db\ActiveRecord
{

    public $full_name;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'clients';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['first_name'], 'required'],
            [['created', 'updated'], 'safe'],
            [['first_name', 'middle_name', 'last_name'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'first_name' => 'First Name',
            'middle_name' => 'Middle Name',
            'last_name' => 'Last Name',
            'created' => 'Created',
            'updated' => 'Updated',
            'full_name' => 'Full Name',
        ];
    }


    public function afterFind()
    {
        parent::afterFind();
        $this->full_name = trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientEmails()
    {
        return $this->hasMany(ClientEmail::class, ['client_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientPhones()
    {
        return $this->hasMany(ClientPhone::class, ['client_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLeads()
    {
        return $this->hasMany(Lead::class, ['client_id' => 'id']);
    }

    public function beforeSave($insert): bool
    {
        if (parent::beforeSave($insert)) {

            if($insert) {
                if(!$this->created) {
                    $this->created = date('Y-m-d H:i:s');
                }
            }

            $this->updated = date('Y-m-d H:i:s');
            return true;
        }
        return false;
    }

}
