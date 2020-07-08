<?php

namespace sales\model\conference\service;

use common\models\Call;
use common\models\Conference;
use common\models\ConferenceParticipant;

class ConferenceDataService
{
    private const TYPE_LISTEN = 'listen';
    private const TYPE_COACHING = 'coaching';

    public static function getDataBySid(string $conferenceSid): array
    {
        $conference = Conference::findOne(['cf_sid' => $conferenceSid, 'cf_status_id' => [Conference::STATUS_START]]);
        if (!$conference) {
            return [];
        }
        return self::getData($conference);
    }

    public static function getDataById(int $conferenceId): array
    {
        $conference = Conference::findOne(['cf_id' => $conferenceId, 'cf_status_id' => [Conference::STATUS_START]]);
        if (!$conference) {
            return [];
        }
        return self::getData($conference);
    }

    private static function getData(Conference $conference): array
    {
        $participants = [];
        $users = [];

        $participantsModels = ConferenceParticipant::find()
            ->andWhere(['cp_cf_id' => $conference->cf_id])
            ->andWhere(['cp_status_id' => [ConferenceParticipant::STATUS_JOIN, ConferenceParticipant::STATUS_HOLD]])
            ->innerJoinWith('cpCall')
            ->orderBy(['cp_id' => SORT_ASC])
            ->all();

        $name = '';
        $avatar = '';
        $phone = '';
        $type = '';

        foreach ($participantsModels as $participant) {
            $call = $participant->cpCall;
            $users[] = $call->c_created_user_id;
            if ($participant->isClient()) {
                $name = $call->cClient->getFullName();
                $avatar = strtoupper($name[0]);
                $phone = '';
                if ($call->isOut()) {
                    $phone = $call->c_to;
                } elseif ($call->isIn()) {
                    $phone = $call->c_from;
                }
                $type = '';
            } elseif ($participant->isAgent()) {
                if ($call->isJoin()) {
                    if ($call->c_source_type_id === Call::SOURCE_COACH) {
                        $type = self::TYPE_COACHING;
                    } elseif ($call->c_source_type_id === Call::SOURCE_LISTEN) {
                        $type = self::TYPE_LISTEN;
                    } else {
                        $type = '';
                    }
                } else {
                    $type = '';
                }
                $name = $call->cCreatedUser->nickname ?? 'agent';
                $avatar = strtoupper($name[0]);
                $phone = '';
            }

            $participants[] = [
                'callSid' => $participant->cp_call_sid,
                'avatar' => $avatar,
                'name' => $name,
                'phone' => $phone,
                'type' => $type,
                'duration' => time() - strtotime($participant->cp_join_dt),
                'userId' => $type === self::TYPE_LISTEN ? $call->c_created_user_id : null
            ];
        }

        return [
            'users' => $users,
            'participants' => $participants,
            'conference' => [
                'sid' => $conference->cf_sid,
                'duration' => time() - strtotime($conference->cf_created_dt),
            ],
        ];
    }
}
