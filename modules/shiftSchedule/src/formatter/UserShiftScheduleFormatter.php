<?php

namespace modules\shiftSchedule\src\formatter;

use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;
use src\logger\formatter\Formatter;

/**
 * Class UserShiftScheduleFormatter
 * @package modules\shiftSchedule\src\formatter
 *
 * @property-read UserShiftSchedule $userShiftSchedule
 */
class UserShiftScheduleFormatter implements Formatter
{
    private UserShiftSchedule $userShiftSchedule;

    public function __construct(UserShiftSchedule $userShiftSchedule)
    {
        $this->userShiftSchedule = $userShiftSchedule;
    }

    /**
     * @param string $attribute
     * @return string
     */
    public function getFormattedAttributeLabel(string $attribute): string
    {
        return $this->userShiftSchedule->getAttributeLabel($attribute);
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
            'uss_created_dt',
            'uss_updated_dt',
            'uss_created_user_id',
            'uss_updated_user_id',
            'uss_year_start',
            'uss_month_start'
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
