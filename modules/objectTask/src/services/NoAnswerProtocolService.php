<?php

namespace modules\objectTask\src\services;

use common\models\Employee;
use common\models\Lead;
use common\models\Notifications;
use common\models\query\EmployeeQuery;
use modules\objectTask\src\entities\ObjectTaskScenario;
use modules\objectTask\src\scenarios\NoAnswer;
use src\helpers\text\StringHelper;
use src\model\leadData\entity\LeadDataQuery;
use src\model\leadDataKey\services\LeadDataKeyDictionary;
use yii\helpers\ArrayHelper;

class NoAnswerProtocolService
{
    public static function leadWasInNoAnswer(Lead $lead): bool
    {
        return (bool) LeadDataQuery::getOneByLeadKeyValue(
            $lead->id,
            LeadDataKeyDictionary::KEY_AUTO_FOLLOW_UP,
            NoAnswer::KEY
        );
    }

    public static function notifyAboutClientAnswer(ObjectTaskScenario $objectTaskScenario, Lead $lead): void
    {
        $parameterService = new ObjectTaskScenarioParameterService($objectTaskScenario);
        $roles = (array) $parameterService->get(
            NoAnswer::PARAMETER_ANSWER_NOTIFICATION . '.' . NoAnswer::PARAMETER_ANSWER_NOTIFICATION_ROLES,
            []
        );
        $notificationTitle = (string) $parameterService->get(
            NoAnswer::PARAMETER_ANSWER_NOTIFICATION . '.' . NoAnswer::PARAMETER_ANSWER_NOTIFICATION_TITLE,
            ''
        );
        $notificationDescription = (string) $parameterService->get(
            NoAnswer::PARAMETER_ANSWER_NOTIFICATION . '.' . NoAnswer::PARAMETER_ANSWER_NOTIFICATION_DESCRIPTION,
            ''
        );

        if (!$roles) {
            return;
        }

        $notificationTitle = StringHelper::parseStringWithObjectTemplate($notificationTitle, $lead);
        $notificationDescription = StringHelper::parseStringWithObjectTemplate($notificationDescription, $lead);
        /** @var Employee[] $employeeList */
        $employeeList = EmployeeQuery::selectByRolesAndProjectId($roles, $lead->project_id)->all();

        foreach ($employeeList as $employee) {
            Notifications::createAndPublish(
                $employee->id,
                $notificationTitle,
                $notificationDescription,
                Notifications::TYPE_INFO
            );
        }
    }
}
