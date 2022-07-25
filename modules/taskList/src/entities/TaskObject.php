<?php

namespace modules\taskList\src\entities;

use common\models\Call;
use common\models\Email;
use common\models\Sms;
use modules\taskList\src\objects\call\CallTaskObject;
use modules\taskList\src\objects\email\EmailTaskObject;
use modules\taskList\src\objects\sms\SmsTaskObject;
use yii\helpers\VarDumper;

class TaskObject
{
    public const OBJ_CALL   = 'call';
    public const OBJ_SMS    = 'sms';
    public const OBJ_EMAIL  = 'email';

    public const OBJ_LIST = [
        self::OBJ_CALL => 'Call',
        self::OBJ_SMS => 'SMS',
        self::OBJ_EMAIL => 'Email',
    ];

    public const OBJ_CLASS_LIST = [
        self::OBJ_CALL => CallTaskObject::class,
        self::OBJ_SMS => SmsTaskObject::class,
        self::OBJ_EMAIL => EmailTaskObject::class,
    ];

    public const OBJ_TASK_LIST = [
        self::OBJ_CALL => Call::class,
        self::OBJ_SMS => Sms::class,
        self::OBJ_EMAIL => Email::class,
    ];

    /**
     * @return string[]
     */
    public static function getObjectList(): array
    {
        return self::OBJ_LIST;
    }

    /**
     * @param string $objectName
     * @return object|null
     * @throws \yii\base\InvalidConfigException
     */
    public static function getByName(string $objectName)
    {
        if (!empty(self::OBJ_CLASS_LIST[$objectName])) {
            $obj = \Yii::createObject(self::OBJ_CLASS_LIST[$objectName]);
        } else {
            $obj = null;
        }
        return $obj;
    }

    /**
     * @param string $objectName
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public static function getAttributeListByObject(string $objectName): array
    {

        $object = self::getByName($objectName);
        $list = $object::getObjectAttributeList();
        /*
        $defaultList = $this->getDefaultAttributeList();
        $objList = [];
        if ($object) {
            $list = $this->getObjectAttributeList();
            if (isset($list[$object]) && is_array($list[$object])) {
                $objList = $list[$object];
            }
        }

        return array_merge($objList, $defaultList);*/
        return $list;
    }
}
