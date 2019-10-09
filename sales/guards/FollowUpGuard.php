<?php

namespace sales\guards;

use Yii;
use common\models\Call;
use common\models\Employee;
use common\models\Lead;

class FollowUpGuard
{

    /**
     * @param Employee $user
     * @param Lead $lead
     */
    public function guard(Employee $user, Lead $lead): void
    {
        if (!$user->isAgent()) {
            return;
        }
        if (!$lead->isAnswered()) {
            return;
        }

        $countCalls = $this->getOutgoingCalls($user->id, $lead->id);

        if ($countCalls < (int)Yii::$app->params['settings']['follow_up_call_min_count']) {
            throw new \DomainException('The Lead can\'t be sent to Follow Up. Not all tasks were complete.');
        }
    }

    /**
     * @param int $userId
     * @param int $leadId
     * @return int
     */
    private function getOutgoingCalls(int $userId, int $leadId): int
    {
        $days = (int)Yii::$app->params['settings']['follow_up_lookup_days'];
        $date = date('Y-m-d', strtotime('-' . $days . ' days'));
        $duration = (int)Yii::$app->params['settings']['follow_up_call_min_time'];

        return Call::find()
            ->andWhere([
                'c_call_type_id' => Call::CALL_TYPE_OUT,
                'c_created_user_id' => $userId,
                'c_lead_id' => $leadId,
            ])
            ->andWhere(['>=', 'c_call_duration', $duration])
            ->andWhere(['>', 'c_created_dt', $date])
            ->count();
    }

}
