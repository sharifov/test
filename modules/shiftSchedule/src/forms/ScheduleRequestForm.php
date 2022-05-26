<?php

namespace modules\shiftSchedule\src\forms;

use common\models\Employee;
use DateTime;
use Exception;
use src\helpers\DateHelper;
use yii\base\Model;
use yii\web\Request;

/**
 *
 * @property-write Request $attributesRequest
 */
class ScheduleRequestForm extends Model
{
    const DESCRIPTION_MAX_LENGTH = 1000;

    /**
     * @var string
     */
    public string $scheduleType = '';
    /**
     * @var string
     */
    public string $description = '';
    /**
     * @var string|null
     */
    public ?string $requestedRangeTime = '';
    /**
     * @var string
     */
    public string $dateTimeStart = '';
    /**
     * @var string
     */
    public string $dateTimeEnd = '';

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [
                [
                    'scheduleType',
                    'description',
                    'requestedRangeTime',
                ],
                'required',
            ],
            [
                'scheduleType',
                'string',
                'max' => 100,
            ],
            [
                'description',
                'string',
                'max' => self::DESCRIPTION_MAX_LENGTH,
            ],
            [
                'requestedRangeTime',
                'convertDateTimeRange'
            ],
        ];
    }

    public function convertDateTimeRange($attribute)
    {
        if ($this->requestedRangeTime) {
            $date = explode(' - ', $this->requestedRangeTime);
            if (count($date) === 2) {
                if (DateHelper::checkDateTime($date[0], 'Y-m-d H:i')) {
                    $this->dateTimeStart = Employee::convertTimeFromUserDtToUTC(strtotime($date[0]));
                } else {
                    $this->addError($attribute, 'Date time start incorrect format');
                    $this->requestedRangeTime = null;
                }
                if (DateHelper::checkDateTime($date[1], 'Y-m-d H:i')) {
                    $this->dateTimeEnd = Employee::convertTimeFromUserDtToUTC(strtotime($date[1]));
                } else {
                    $this->addError($attribute, 'Date time end incorrect format');
                    $this->requestedRangeTime = null;
                }
            } else {
                $this->addError($attribute, 'Requested Range Time is not parsed correctly');
                $this->requestedRangeTime = null;
            }
        }
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'requestedRangeTime' => 'Date Range From / To',
            'scheduleType' => 'Schedule Type',
            'description' => 'Description',
        ];
    }

    /**
     * Setting attribute from request data
     * @param Request $request
     * @return void
     * @throws Exception
     */
    public function setAttributesRequest(Request $request): void
    {
        if (!empty($start = $request->get('start')) && !empty($end = $request->get('end'))) {
            if (DateHelper::checkDateTime($start, 'Y-m-d') && DateHelper::checkDateTime($end, 'Y-m-d H:i')) {
                $this->requestedRangeTime = sprintf(
                    '%s - %s',
                    (new DateTime($start))->format('Y-m-d H:i'),
                    (new DateTime($end))->format('Y-m-d H:i')
                );
            }
        }
    }
}
