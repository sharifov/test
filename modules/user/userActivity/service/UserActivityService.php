<?php

namespace modules\user\userActivity\service;

use common\models\UserOnline;
use modules\user\src\events\UserEvents;
use modules\user\userActivity\entity\UserActivity;
use Yii;
use yii\helpers\VarDumper;

/**
 * Class UserActivityService
 *
 */
class UserActivityService
{
    /**
     * @return array
     */
    public static function checkUserActivity(): array
    {
        $result = [];
        $userList = UserOnline::find()->all();
        if ($userList) {
            foreach ($userList as $item) {
                $shiftEventId = null;
                $dateTime = date('Y-m-d H:i:s');
                self::addEvent(
                    $item->uo_user_id,
                    UserEvents::EVENT_ONLINE,
                    $dateTime,
                    null,
                    $dateTime,
                    $shiftEventId,
                    UserActivity::TYPE_MONITORING
                );

                $isActive = false;
                if ($item->isActive()) {
                    self::addEvent(
                        $item->uo_user_id,
                        UserEvents::EVENT_DEVICE_ACTIVE,
                        $dateTime,
                        null,
                        $dateTime,
                        $shiftEventId,
                        UserActivity::TYPE_MONITORING
                    );
                    $isActive = true;
                }

                $user = $item->uoUser;
                if ($user->userStatus->isOnCall()) {
                    $isActive = true;
                    self::addEvent(
                        $item->uo_user_id,
                        UserEvents::EVENT_ON_CALL,
                        $dateTime,
                        null,
                        $dateTime,
                        $shiftEventId,
                        UserActivity::TYPE_MONITORING
                    );
                }

                if ($isActive) {
                    self::addEvent(
                        $item->uo_user_id,
                        UserEvents::EVENT_ACTIVE,
                        $dateTime,
                        null,
                        $dateTime,
                        $shiftEventId,
                        UserActivity::TYPE_MONITORING
                    );
                }

                $result[] = $item->uo_user_id;
            }
        }
        return $result;
    }

    /**
     * @param int $userId
     * @param string $event
     * @param string $startDt
     * @param int|null $objectId
     * @param string|null $endDt
     * @param int|null $shiftEventId
     * @param int|null $typeId
     * @param string|null $description
     * @return bool
     */
    public static function addEvent(
        int $userId,
        string $event,
        string $startDt,
        ?int $objectId,
        ?string $endDt = null,
        ?int $shiftEventId = null,
        ?int $typeId = null,
        ?string $description = null,
    ): bool {
        $ua = new UserActivity();
        $ua->ua_user_id = $userId;
        $ua->ua_object_event = $event;
        $ua->ua_start_dt = $startDt;

        if ($endDt) {
            $ua->ua_end_dt = $endDt;
        } else {
            $ua->ua_end_dt = $ua->ua_start_dt;
        }

        $ua->ua_shift_event_id = $shiftEventId;
        $ua->ua_object_id = $objectId;
        $ua->ua_type_id = $typeId;
        $ua->ua_description = $description;

        if (!$response = $ua->save()) {
            \Yii::error(['message' => 'Not added UserActivity',
                'errors' => $ua->getErrors(),
                'data' => $ua->attributes], 'UserActivityService:addEvent:save');
        }
        return $response;
    }

    /**
     * @param int $userId
     * @param string $fromDateTime
     * @param string $toDateTime
     * @param string|null $eventName
     * @param int $delayMin
     * @param int $minimumDuration
     * @param string|null $type
     * @return array
     */
    public static function getUniteEventsByUserId(
        int $userId,
        string $fromDateTime,
        string $toDateTime,
        ?string $eventName = null,
        int $delayMin = 3,
        int $minimumDuration = 3,
        ?string $type = null
    ): array {

        $data = [];
        $query = UserActivity::find()->where(['ua_user_id' => $userId]); //, 'toDate(ua_start_dt)' => $fromDateTime
        $query->andWhere(['between', 'ua_start_dt', $fromDateTime, $toDateTime]);

        if ($eventName) {
            $query->andWhere(['ua_object_event' => $eventName]);
        }
        $eventList = $query->orderBy(['ua_start_dt' => SORT_ASC])->asArray()->all();
       // VarDumper::dump($eventList, 10, true); echo '<hr>';
        if ($eventList) {
            $nr = 0;
            foreach ($eventList as $n => $event) {
                $sec = strtotime($event['ua_start_dt']);
                $dt = date('Y-m-d H:i', $sec);
                $dtSec = strtotime($dt);

                if (isset($eventList[$n - 1])) {
                    $prevEvent = $eventList[$n - 1];
                    $dtPrev = date('Y-m-d H:i', strtotime($prevEvent['ua_start_dt']));
                    $dtSecPrev = strtotime($dtPrev);

                    if ($dtSec - $dtSecPrev >= (60 * $delayMin)) {
                        $nr++;
                    }
                }
                $data[$nr][] = $dt;
            }
        }

        if ($data) {
            $data = self::implodePeriodEventList($data, $minimumDuration, $type);
        }
        unset($eventList);

        return $data;
    }

    /**
     * @param array $eventList
     * @param int $minimumDuration
     * @param string|null $type
     * @return array
     */
    public static function implodePeriodEventList(array $eventList, int $minimumDuration = 1, ?string $type = null): array
    {
        $data = [];
        if ($eventList) {
            foreach ($eventList as $list) {
                $start = $list[0];
                $end = end($list);
                $duration = (int) (strtotime($end) - strtotime($start)) / 60;
                if ($duration >= $minimumDuration) {
                    //VarDumper::dump($list, 10, true);
                    $data[] = [
                        'start' => $start,
                        'end' => $end,
                        'duration' => $duration,
                        'list' => $list,
                        'type' => $type
                    ];
                }
            }
        }
        return $data;
    }
}
