<?php

namespace sales\model\notification\events;

use common\models\Notifications;
use yii\base\Component;
use yii\base\Event;
use yii\helpers\VarDumper;


//class MessageEvent extends Event
//{
//    public $message;
//}

class NotificationEvents extends Component
{

    public const EVENT_NOTIFY_ALL = 'notification.*';

    public const EVENT_NOTIFY_SENT = 'notification.sent';
    public const EVENT_NOTIFY_UPDATE = 'notification.update';
    public const EVENT_NOTIFY_DELETE = 'notification.delete';



    public function send($params): void
    {

        //$sender = $params->sender;

        //$event = new MessageEvent;
        //$event->message = $message;
        //$this->trigger(self::EVENT_MESSAGE_SENT, $event);
        \Yii::warning(VarDumper::dumpAsString($params->data), 'NotificationEvents');
    }

    public function send2($params): void
    {

        //$sender = $params->sender;

        //$event = new MessageEvent;
        //$event->message = $message;
        //$this->trigger(self::EVENT_MESSAGE_SENT, $event);
        \Yii::error(VarDumper::dumpAsString($params->data), 'NotificationEvents');
    }
}