<?php

namespace frontend\widgets\notification;

use common\models\Email;
use common\models\Notifications;
use yii\bootstrap4\Html;
use yii\helpers\StringHelper;
use yii\helpers\Url;

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
        return [
            'notification' => [
                'command' => self::COMMAND_ADD,
                'id' => $ntf->n_id,
                'url' => Url::to(['/notifications/view2', 'id' => $ntf->n_id]),
                'title' => Html::encode($ntf->n_title),
                'time' => strtotime($ntf->n_created_dt),
                'message' => StringHelper::truncate(Email::strip_html_tags($ntf->n_message), 80, '...'),
                'type' => $ntf->getNotifyType(),
                'popup' => (int)$ntf->n_popup,
                'notifyMessage' => (int)$ntf->n_popup ? str_replace(["\r\n", "\n", '"'], ['', '', '\"'], $ntf->n_message) : '',
                'notifyDesktopMessage' => (int)$ntf->n_popup ? str_replace('"', '\"', strip_tags($ntf->n_message)) : '',
            ]
        ];
    }

    public static function delete(int $id): array
    {
        return [
            'notification' => [
                'command' => self::COMMAND_DELETE,
                'id' => $id,
            ]
        ];
    }

    public static function deleteAll(): array
    {
        return [
            'notification' => [
                'command' => self::COMMAND_DELETE_ALL,
            ]
        ];
    }
}
