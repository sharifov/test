<?php

namespace frontend\widgets\notification;

use common\components\purifier\Purifier;
use common\components\purifier\PurifierFilter;
use common\models\Notifications;
use yii\bootstrap4\Html;
use src\helpers\text\StringHelper;

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
    private const COMMAND_DELETE_BATCH = 'delete_batch';
    private const COMMAND_DELETE_ALL = 'delete_all';

    /**
     * @param Notifications $ntf
     * @return array
     */
    public static function add(Notifications $ntf): array
    {
        $message = Purifier::purify($ntf->n_message, PurifierFilter::shortCodeToId());
        return [
            'notification' => [
                'command' => self::COMMAND_ADD,
                'userId' => $ntf->n_user_id,
                'id' => $ntf->n_id,
                'title' => Html::encode($ntf->n_title),
                'time' => strtotime($ntf->n_created_dt),
                'message' => StringHelper::truncate(StringHelper::stripHtmlTags($message), 80, '...'),
                'type' => Notifications::getNotifyType($ntf->n_type_id),
                'popup' => (int)$ntf->n_popup,
                'notifyMessage' => (int)$ntf->n_popup ? str_replace(["\r\n", "\n", '"'], ['', '', '\"'], strip_tags($message)) : '',
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

    public static function deleteBatch(array $ids, int $userId): array
    {
        return [
            'notification' => [
                'command' => self::COMMAND_DELETE_BATCH,
                'userId' => $userId,
                'ids' => $ids,
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

    public static function desktopMessage(
        string $desktopId,
        string $title,
        string $message,
        string $type,
        string $desktopMessage,
        bool $showBrowserNotify
    ): array {
        return [
            'data' => [
                'desktopId' => $desktopId,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'desktopMessage' => $desktopMessage,
                'showBrowserNotify' => (int)$showBrowserNotify
            ]
        ];
    }
}
