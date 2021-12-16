<?php

namespace sales\model\department\department;

/**
 * Class CallDistributionSort
 * @package sales\model\department\department
 *
 * @property int|null $generalLineCallCount
 * @property int|null $phoneReadyTime
 * @property int|null $priorityLevel
 * @property int|null $grossProfit
 */
class CallDistributionSort
{
    public ?int $generalLineCallCount = null;
    public ?int $phoneReadyTime = null;
    public ?int $priorityLevel = null;
    public ?int $grossProfit = null;

    private array $sort = [
        'ASC' => SORT_ASC,
        'DESC' => SORT_DESC
    ];

    public function __construct(array $params)
    {
        if (!empty($params['general_line_call_count'])) {
            $this->generalLineCallCount = $this->sort[mb_strtoupper($params['general_line_call_count'])] ?? null;
        }

        if (!empty($params['phone_ready_time'])) {
            $this->phoneReadyTime = $this->sort[mb_strtoupper($params['phone_ready_time'])] ?? SORT_ASC;
        }

        if (!empty($params['priority_level'])) {
            $this->priorityLevel = $this->sort[mb_strtoupper($params['priority_level'])] ?? SORT_DESC;
        }

        if (!empty($params['gross_profit'])) {
            $this->grossProfit = $this->sort[mb_strtoupper($params['gross_profit'])] ?? SORT_DESC;
        }
    }
}
