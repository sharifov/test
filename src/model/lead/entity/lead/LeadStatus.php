<?php

namespace src\model\lead\entity\lead;

use common\models\Lead;

class LeadStatus
{
    public static function getStatusLabel(int $status): string
    {
        $label = '';
        switch ($status) {
            case Lead::STATUS_PENDING:
                $label = '<span class="label status-label bg-light-brown">' . Lead::getStatus($status) . '</span>';
                break;
            case Lead::STATUS_SNOOZE:
            case Lead::STATUS_PROCESSING:
                $label = '<span class="label status-label bg-turquoise">' . Lead::getStatus($status) . '</span>';
                break;
            case Lead::STATUS_ON_HOLD:
            case Lead::STATUS_FOLLOW_UP:
                $label = '<span class="label status-label bg-blue">' . Lead::getStatus($status) . '</span>';
                break;
            case Lead::STATUS_SOLD:
            case Lead::STATUS_BOOKED:
                $label = '<span class="label status-label bg-green">' . Lead::getStatus($status) . '</span>';
                break;
            case Lead::STATUS_TRASH:
            case Lead::STATUS_REJECT:
            case Lead::STATUS_CLOSED:
                $label = '<span class="label status-label bg-red">' . Lead::getStatus($status) . '</span>';
                break;
            case Lead::STATUS_NEW:
            case Lead::STATUS_BOOK_FAILED:
            case Lead::STATUS_ALTERNATIVE:
            case Lead::STATUS_EXTRA_QUEUE:
                $label = '<span class="label label-default">' . Lead::getStatus($status) . '</span>';
                break;
        }
        return $label;
    }
}
