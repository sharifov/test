<?php

namespace modules\taskList\src\services;

use modules\taskList\src\entities\taskList\TaskList;
use modules\taskList\src\entities\TaskObject;

class TaskListService
{
    public static function getObjectAttributeList(string $object)
    {
        //$object
        //$list = Yii::$app->abac->getAttributeListByObject($this->ap_object);
        return $list;
    }

    /**
     * @param string|null $object
     * @return array
     */
    final public function getAttributeListByObject(?string $object = null): array
    {

        $object = TaskObject::getByName($object);

        $defaultList = $this->getDefaultAttributeList();
        $objList = [];
        if ($object) {
            $list = $this->getObjectAttributeList();
            if (isset($list[$object]) && is_array($list[$object])) {
                $objList = $list[$object];
            }
        }

        return array_merge($objList, $defaultList);
    }
}
