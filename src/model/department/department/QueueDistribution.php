<?php

namespace src\model\department\department;

/**
 * Class QueueDistribution
 *
 * @property int|null $timeStartCallUserAccessGeneral
 * @property int|null $generalLineUserLimit
 * @property int|null $timeRepeatCallUserAccess
 * @property-read ?CallDistributionSort $callDistributionSort
 */
class QueueDistribution
{
    public ?int $timeStartCallUserAccessGeneral;
    public ?int $generalLineUserLimit;
    public ?int $timeRepeatCallUserAccess;
    public ?CallDistributionSort $callDistributionSort = null;

    public function __construct(array $params)
    {
        if (isset($params['time_start_call_user_access_general'])) {
            $this->timeStartCallUserAccessGeneral = (int)$params['time_start_call_user_access_general'];
        } else {
            $this->timeStartCallUserAccessGeneral = null;
        }

        if (isset($params['general_line_user_limit'])) {
            $this->generalLineUserLimit = (int)$params['general_line_user_limit'];
        } else {
            $this->generalLineUserLimit = null;
        }

        if (isset($params['time_repeat_call_user_access'])) {
            $this->timeRepeatCallUserAccess = (int)$params['time_repeat_call_user_access'];
        } else {
            $this->timeRepeatCallUserAccess = null;
        }

        if (!empty($params['call_distribution_sort'])) {
            $this->callDistributionSort = new CallDistributionSort($params['call_distribution_sort']);
        }
    }
}
