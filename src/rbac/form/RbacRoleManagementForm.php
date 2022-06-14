<?php

namespace src\rbac\form;

use yii\base\Model;
use yii\db\Query;
use yii\rbac\Item;

class RbacRoleManagementForm extends Model
{
    public $name;
    public $donor_name;
    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['name', 'donor_name', ], 'required'],
            [['name', 'donor_name'], 'string'],
            [['name'],  'issetRole', 'message' => 'Role does not exist'],
        ];
    }

    public function issetRole($attribute, $value)
    {
        $result = (bool)(new Query())
            ->from(['a' => 'auth_item'])
            ->where(['a.name' => $this->name])
            ->andWhere(['a.type' => Item::TYPE_ROLE])
            ->count();
        $result2 = (bool)(new Query())
            ->from(['a' => 'auth_item'])
            ->where(['a.name' => $this->donor_name])
            ->andWhere(['a.type' => Item::TYPE_ROLE])
            ->count();
        if (!$result || !$result2) {
            $this->addError($attribute, 'Role does not exist');
        }
    }

    /**
     * @return string[]
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Affected Role Name',
            'donor_name' => 'Donor Role Name',
        ];
    }

    public function formName()
    {
        return '';
    }
}
