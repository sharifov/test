<?php

namespace src\logger\formatter;

use common\models\Employee;

/**
 * Class EmployeeFormatter
 * @package src\logger\formatter
 *
 * @property Employee $employee
 */
class EmployeeFormatter implements Formatter
{
    /**
     * @var Employee
     */
    private $employee;

    /**
     * EmployeeFormatter constructor.
     * @param Employee $employee
     */
    public function __construct(Employee $employee)
    {
        $this->employee = $employee;
    }

    /**
     * @param string $attribute
     * @return string
     */
    public function getFormattedAttributeLabel(string $attribute): string
    {
        return $this->employee->getAttributeLabel($attribute);
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
            'created_at',
            'updated_at',
        ];
    }

    /**
     * @return array
     */
    private function getAttributeFormatters(): array
    {
        $employee = $this->employee;
        return [
            'status' => static function ($value) use ($employee) {
                return $employee::STATUS_LIST[$value] ?? $value;
            }
        ];
    }
}
