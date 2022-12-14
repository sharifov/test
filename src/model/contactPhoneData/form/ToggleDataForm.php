<?php

namespace src\model\contactPhoneData\form;

use src\model\contactPhoneData\service\ContactPhoneDataDictionary;
use src\model\contactPhoneList\entity\ContactPhoneList;
use yii\base\Model;

/**
 * Class ToggleDataForm
 */
class ToggleDataForm extends Model
{
    public $modelId;
    public $key;

    public function rules(): array
    {
        return [
            ['modelId', 'required'],
            ['modelId', 'integer'],
            ['modelId', 'exist', 'skipOnError' => true, 'targetClass' => ContactPhoneList::class, 'targetAttribute' => ['modelId' => 'cpl_id']],

            ['key', 'required'],
            ['key', 'string'],
            ['key', 'string', 'max' => 30],
            [['key'], 'in', 'range' => array_keys(ContactPhoneDataDictionary::KEY_LIST)],
        ];
    }

    public function formName()
    {
        return '';
    }
}
