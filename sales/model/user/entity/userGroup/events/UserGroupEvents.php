<?php
namespace sales\model\user\entity\userGroup\events;

use common\components\BackOffice;
use yii\base\Component;
use yii\base\Exception;
use yii\helpers\VarDumper;

class UserGroupEvents extends Component
{
    public const UPDATE         = 'update';
    public const INSERT         = 'insert';
    public const DELETE         = 'delete';

    public const UPDATE_NAME        = 'update.name';
    public const UPDATE_KEY         = 'update.key';
    public const UPDATE_DISABLE     = 'update.disable';

    /**
     * @param $params
     */
    public function webHookUpdate($params): void
    {

        $data['object'] = 'user-group';
        $data['action'] = 'update';
        $data['data'] = $params->data;
        BackOffice::webHook($data);

        //$sender = $params->sender;

        //$event = new MessageEvent;
        //$event->message = $message;
        //$this->trigger(self::EVENT_MESSAGE_SENT, $event);
        //\Yii::warning(VarDumper::dumpAsString($params), 'UserGroupEvents:webHookUpdate');
    }

    /**
     * @param $params
     */
    public function webHookInsert($params): void
    {
        $data['object'] = 'user-group';
        $data['action'] = 'insert';
        $data['data'] = $params->data;
        BackOffice::webHook($data);

        //\Yii::warning(VarDumper::dumpAsString($params->data), 'UserGroupEvents:webHookInsert');
    }

    /**
     * @param $params
     */
    public function webHookDelete($params): void
    {
        $data['object'] = 'user-group';
        $data['action'] = 'delete';
        $data['data'] = $params->data;
        BackOffice::webHook($data);

        //\Yii::warning(VarDumper::dumpAsString($params->data), 'UserGroupEvents:webHookDelete');
    }





}