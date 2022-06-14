<?php

namespace modules\shiftSchedule\src\formatter;

use modules\shiftSchedule\src\entities\shiftScheduleRequest\ShiftScheduleRequest;
use src\logger\formatter\Formatter;

/**
 * Class UserShiftScheduleFormatter
 * @package modules\shiftSchedule\src\formatter
 *
 * @property-read ShiftScheduleRequest $shiftScheduleRequest
 */
class ShiftScheduleRequestFormatter implements Formatter
{
    private ShiftScheduleRequest $shiftScheduleRequest;

    public function __construct(ShiftScheduleRequest $shiftScheduleRequest)
    {
        $this->shiftScheduleRequest = $shiftScheduleRequest;
    }

    /**
     * @param string $attribute
     * @return string
     */
    public function getFormattedAttributeLabel(string $attribute): string
    {
        return $this->shiftScheduleRequest->getAttributeLabel($attribute);
    }

    /**
     * @param $attribute
     * @param $value
     * @return mixed
     */
    public function getFormattedAttributeValue($attribute, $value)
    {
        $functions = $this->getAttributeFormatters();

        if (array_key_exists($attribute, $functions)) {
            return $functions[$attribute]($value);
        }

        return $value;
    }

    /**
     * @return array
     */
    public function getExceptedAttributes(): array
    {
        return [
            'ssr_id',
            'ssr_uss_id',
            'ssr_sst_id',
            'ssr_created_dt',
            'ssr_updated_dt',
            'ssr_created_user_id',
            'ssr_updated_user_id',
        ];
    }

    /**
     * @return array
     */
    private function getAttributeFormatters(): array
    {
        return [];
    }
}
