<?php

namespace modules\notification\src\abac\dto;

use common\models\Employee;
use stdClass;
use common\models\Notifications;

/**
 * Class NotificationAbacDto
 * @package modules\notification\src\abac\dto
 */
class NotificationAbacDto extends stdClass
{
    public int $type;
    public ?string $title;
    public int $userId;

    public function __construct(?Notifications $notification)
    {
        if ($notification) {
            $this->type = $notification->n_type_id;
            $this->title = $notification->n_title;
            $this->userId = $notification->n_user_id;
        }
    }
}
