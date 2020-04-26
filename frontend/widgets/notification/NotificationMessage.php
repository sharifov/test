<?php

namespace frontend\widgets\notification;

use common\components\Purifier;
use common\models\Email;
use common\models\Notifications;
use yii\bootstrap4\Html;
use yii\helpers\StringHelper;

/**
 * Class NotificationMessage
 *
 * @property string $command
 * @property int $id
 * @property Notifications|null $notification
 * @property string $type
 */
class NotificationMessage
{
    private const COMMAND_ADD = 'add';
    private const COMMAND_DELETE = 'delete';
    private const COMMAND_DELETE_ALL = 'delete_all';

    /**
     * @param Notifications $ntf
     * @return array
     */
    public static function add(Notifications $ntf): array
    {
        $message = Purifier::replaceCodesToId($ntf->n_message);
        return [
            'notification' => [
                'command' => self::COMMAND_ADD,
                'userId' => $ntf->n_user_id,
                'id' => $ntf->n_id,
                'title' => Html::encode($ntf->n_title),
                'time' => strtotime($ntf->n_created_dt),
                'message' => StringHelper::truncate(Email::strip_html_tags($message), 80, '...'),
                'type' => $ntf->getNotifyType(),
                'popup' => (int)$ntf->n_popup,
                'notifyMessage' => (int)$ntf->n_popup ? str_replace(["\r\n", "\n", '"'], ['', '', '\"'], $message) : '',
                'notifyDesktopMessage' => (int)$ntf->n_popup ? str_replace('"', '\"', strip_tags($message)) : '',
            ]
        ];
    }

    public static function delete(Notifications $ntf): array
    {
        return [
            'notification' => [
                'command' => self::COMMAND_DELETE,
                'userId' => $ntf->n_user_id,
                'id' => $ntf->n_id,
            ]
        ];
    }

    public static function deleteAll(int $user_id): array
    {
        return [
            'notification' => [
                'command' => self::COMMAND_DELETE_ALL,
                'userId' => $user_id,
            ]
        ];
    }
}
