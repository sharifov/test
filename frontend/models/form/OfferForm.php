<?php

namespace frontend\models\form;

use common\models\Employee;
use common\models\Lead;
use Yii;
use yii\base\Model;

/**
 * This is the Form class for table "offer".
 *
 * @property int $of_id
//  @property string $of_gid
//  @property string $of_uid
 * @property string $of_name
 * @property int $of_lead_id
 * @property int $of_status_id
 * @property int $of_owner_user_id
 *
 */
class OfferForm extends Model
{

    public $of_id;
//    public $of_gid;
//    public $of_uid;
    public $of_name;
    public $of_lead_id;
    public $of_status_id;
    public $of_owner_user_id;


    public function rules()
    {
        return [
            [['of_lead_id'], 'required'],
            [['of_lead_id', 'of_status_id', 'of_owner_user_id'], 'integer'],
//            [['of_gid'], 'string', 'max' => 32],
//            [['of_uid'], 'string', 'max' => 15],
            [['of_name'], 'string', 'max' => 40],
//            [['of_gid'], 'unique'],
//            [['of_uid'], 'unique'],
            [['of_lead_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['of_lead_id' => 'id']],
            [['of_owner_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['of_owner_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'of_id'                 => 'ID',
//            'of_gid'                => 'Gid',
//            'of_uid'                => 'Uid',
            'of_name'               => 'Name',
            'of_lead_id'            => 'Lead ID',
            'of_status_id'          => 'Status ID',
            'of_owner_user_id'      => 'Owner User ID',
        ];
    }

}
