<?php

namespace src\helpers\lead;

use common\components\i18n\Formatter;
use common\models\Department;
use common\models\Employee;
use common\models\Lead;
use DateTime;
use modules\lead\src\abac\dto\LeadAbacDto;
use modules\lead\src\abac\LeadAbacObject;
use src\access\EmployeeDepartmentAccess;
use src\auth\Auth;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class LeadHelper
{
    private static array $departments = [];

    /**
     * @return array
     */
    public static function tripTypeList(): array
    {
        return [
            Lead::TRIP_TYPE_ONE_WAY => 'One Way',
            Lead::TRIP_TYPE_ROUND_TRIP => 'Round Trip',
            Lead::TRIP_TYPE_MULTI_DESTINATION => 'Multi destination'
        ];
    }

    /**
     * @param string|null $type
     * @return string|null
     */
    public static function tripTypeName(?string $type): ?string
    {
        return ArrayHelper::getValue(self::tripTypeList(), $type);
    }

    /**
     * @return array
     */
    public static function cabinList(): array
    {
        return [
            Lead::CABIN_ECONOMY => 'Economy',
            Lead::CABIN_PREMIUM => 'Premium eco',
            Lead::CABIN_BUSINESS => 'Business',
            Lead::CABIN_FIRST => 'First',
        ];
    }


    /**
     * @param string|null $type
     * @return string|null
     */
    public static function cabinName(?string $type): ?string
    {
        return ArrayHelper::getValue(self::cabinList(), $type);
    }

    /**
     * @return array
     */
    public static function statusList(): array
    {
        return [
            Lead::STATUS_PENDING        => 'Pending',
            Lead::STATUS_PROCESSING     => 'Processing',
            Lead::STATUS_REJECT         => 'Reject',
            Lead::STATUS_FOLLOW_UP      => 'Follow Up',
            Lead::STATUS_ON_HOLD        => 'Hold On',
            Lead::STATUS_SOLD           => 'Sold',
            Lead::STATUS_TRASH          => 'Trash',
            Lead::STATUS_BOOKED         => 'Booked',
            Lead::STATUS_SNOOZE         => 'Snooze',
        ];
    }

    /**
     * @param string|null $status
     * @return string|null
     */
    public static function statusName(?string $status): ?string
    {
        return ArrayHelper::getValue(self::statusList(), $status);
    }

    /**
     * @return array
     */
    public static function adultsChildrenInfantsList(): array
    {
        return array_combine(range(0, 9), range(0, 9));
    }

    /**
     * @param Lead $lead
     * @return array
     */
    public static function getAllOriginsByLead(Lead $lead): array
    {
        $result = [];
        foreach ($lead->leadFlightSegments as $segment) {
            $result[] = $segment->origin;
        }
        return $result;
    }

    /**
     * @param Lead $lead
     * @return array
     */
    public static function getAllDestinationByLead(Lead $lead): array
    {
        $result = [];
        foreach ($lead->leadFlightSegments as $segment) {
            $result[] = $segment->destination;
        }
        return $result;
    }

    /**
     * @param Lead $lead
     * @return array
     */
    public static function getIataByLead(Lead $lead): array
    {
        $result = [];
        foreach ($lead->leadFlightSegments as $key => $segment) {
            if ($key === 0) {
                $result[] = $segment->origin;
            }
            if ($key > 0 && $result[count($result) - 1] !== $segment->origin) {
                $result[] = $segment->origin;
            }
            $result[] = $segment->destination;
        }
        return $result;
    }

    public static function expirationNowDiffInSeconds(string $expirationDt)
    {
        $deadLineTsp = (new DateTime($expirationDt))->getTimestamp();
        $nowTsp = (new DateTime('now'))->getTimestamp();
        return $deadLineTsp - $nowTsp;
    }

    public static function expiredLead(Lead $lead)
    {
        return (self::expirationNowDiffInSeconds($lead->l_expiration_dt) <= 0);
    }

    public static function displayLeadPoorProcessingTimer(string $expirationDt, string $ruleName, string $style = ''): string
    {
        $secondsDiff = self::expirationNowDiffInSeconds($expirationDt);
        $class = 'label label-info';
        if ($secondsDiff <= 0) {
            $class = 'label label-danger';
        } else if ($secondsDiff <= 3600) {
            $class = 'label label-warning';
        }
        $timer = Html::tag('span', '', [
            'class' => 'enable-timer-lpp',
            'data-seconds' => $secondsDiff,
            'data-toggle' => 'tooltip',
            'data-html' => 'true',
            'data-original-title' => 'Rule Name: ' . $ruleName . '; <br> Expiration Dt: ' . \Yii::$app->formatter->asDatetime(strtotime($expirationDt)),
        ]);
        return Html::tag('span', '<i class="fa fa-clock-o"></i> ' . $timer, [
            'class' => $class,
            'style' => $style
        ]);
    }

    public static function getDepartments(\frontend\components\User $user): array
    {
        if (self::$departments) {
            return self::$departments;
        }

        self::$departments = EmployeeDepartmentAccess::getDepartments($user->identity);
        ksort(self::$departments);
        self::$departments = array_merge(
            [0 => '-'],
            self::$departments,
        );

        return self::$departments;
    }

    public static function displaySnoozeFor(Lead $lead, int $timeNow, string $style = ''): string
    {
        if ($lead->isSnoozeExpired($timeNow)) {
            $content = 'Pause';
        } else {
            $content = \Yii::$app->formatter->format($lead->snooze_for, 'byUserDateTime');
        }
        return Html::tag('span', $content, ['class' => 'label label-info', 'style' => $style]);
    }

    public static function isShowLppTimer(Lead $lead, ?int $userId = null): bool
    {
        if (!$lead->isProcessing()) {
            return false;
        }

        $leadAbacDto = new LeadAbacDto($lead, $userId ?? (int) Auth::id());
        /** @abac $leadAbacDto, LeadAbacObject::OBJ_EXTRA_QUEUE, LeadAbacObject::ACTION_ACCESS, show timer in lead/view */
        $isAbacLppTimer = \Yii::$app->abac->can($leadAbacDto, LeadAbacObject::OBJ_EXTRA_QUEUE, LeadAbacObject::ACTION_ACCESS);
        if (!$isAbacLppTimer) {
            return false;
        }

        return (bool) ($lead->minLpp->lpp_expiration_dt ?? null);
    }
}
