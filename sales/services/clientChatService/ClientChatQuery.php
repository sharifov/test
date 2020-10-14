<?php

namespace sales\services\clientChatService;

use common\models\Employee;
use sales\helpers\query\QueryHelper;
use sales\model\clientChat\dashboard\FilterForm;
use sales\model\clientChat\entity\ClientChat;

/**
 * Class ClientChatQuery
 */
class ClientChatQuery
{
    public static function countFreeToTake(Employee $user, array $channelsIds, FilterForm $filter): int
    {
        $query = ClientChat::find()->byStatus(ClientChat::STATUS_IDLE);

        if (ClientChat::isTabActive($filter->status)) {
            $query->active();
        } elseif (ClientChat::isTabClosed($filter->status)) {
            $query->archive();
        }

        if ($filter->channelId) {
            $query->byChannel($filter->channelId);
        } else {
            $query->byChannelIds($channelsIds);
        }

        if ($filter->project) {
            $query->byProject($filter->project);
        }

        if ($filter->userId) {
            $query->andWhere(['cch_owner_user_id' => $filter->userId]);
        }

        if ($filter->fromDate && $filter->toDate) {
            QueryHelper::dateRangeByUserTZ(
                $query,
                'cch_created_dt',
                $filter->fromDate,
                $filter->toDate,
                $user->timezone
            );
        }

        return (int) $query->count();
    }
}
