<?php

namespace src\services\email;

use src\repositories\lead\LeadRepository;
use src\repositories\cases\CasesRepository;
use common\models\Lead;
use src\entities\cases\Cases;
use common\models\ClientEmail;
use common\models\UserProjectParams;
use common\models\Employee;
use common\models\DepartmentEmailProject;

/**
 *
 * class EmailServiceHelper
 *
 * @param LeadRepository $leadRepository
 * @param CasesRepository $casesRepository
 *
 */
class EmailServiceHelper
{
    private $leadRepository;
    private $casesRepository;

    public function __construct(LeadRepository $leadRepository, CasesRepository $casesRepository)
    {
        $this->leadRepository = $leadRepository;
        $this->casesRepository = $casesRepository;
    }

    /**
     * @param string $subject
     * @param string $refMessageId
     * @return int|null
     */
    public function detectLeadId(string $subject, string $refMessageId): ?int
    {
        try {
            $lead = $this->getLeadFromSubjectByIdOrHash($subject);
            if (!$lead) {
                $lead = $this->getLeadFromSubjectByKivTag($refMessageId);
            }
        } catch (NotFoundException $exception) {
            Yii::info('(' . $exception->getCode() . ') ' . $exception->getMessage() . ' File: ' . $exception->getFile() . '(Line: ' . $exception->getLine() . ')' . '; message_id: ' . $refMessageId, 'info\EmailServiceHelper:detectLeadId:NotFoundException');
        }

        return $lead->id ?? null;
    }

    /**
     * @param string $subject
     * @param string $refMessageId
     * @return int|null
     */
    public function detectCaseId(string $subject, string $refMessageId): ?int
    {
        try {
            $case = $this->getCaseFromSubjectByIdOrHash($subject);

            if (!$case) {
                $case = $this->getCaseFromSubjectByKivTag($refMessageId);
            }
        } catch (NotFoundException $exception) {
            Yii::info('(' . $exception->getCode() . ') ' . $exception->getMessage() . ' File: ' . $exception->getFile() . '(Line: ' . $exception->getLine() . ')' . '; message_id: ' . $refMessageId, 'info\EmailServiceHelper:detectCaseId:NotFoundException');
        }

        return $case->cs_id ?? null;
    }


    /**
     *
     * @param string $email
     * @return int|NULL
     */
    public function detectClientId(string $email): ?int
    {
        $clientEmail = ClientEmail::find()->byEmail($email)->one();
        return $clientEmail->client_id ?? null;
    }

    /**
     * @param string|null $subject
     * @return Cases
     */
    private function getCaseFromSubjectByIdOrHash(?string $subject): ?Cases
    {
        if (!$subject) {
            return null;
        }

        preg_match('~\[cid:(\d+)\]~si', $subject, $matches);

        if (!empty($matches[1])) {
            $case_id = (int) $matches[1];
            $case = $this->casesRepository->find($case_id);
        }

        if (empty($case)) {
            $matches = [];
            preg_match('~\[gid:(\w+)\]~si', $subject, $matches);
            if (!empty($matches[1])) {
                $case = $this->casesRepository->findByGid((int)$matches[1]);
            }
        }

        return $case ?? null;
    }

    /**
     * @param string|null $refMessageId
     * @return Cases|null
     */
    private function getCaseFromSubjectByKivTag(?string $refMessageId): ?Cases
    {
        if (!$refMessageId) {
            return null;
        }

        $matches = [];
        preg_match_all('~<kiv\.(.+)>~iU', $refMessageId, $matches);
        if (!empty($matches[1])) {
            foreach ($matches[1] as $messageId) {
                $messageArr = explode('.', $messageId);
                $caseId = end($messageArr);
                if (!empty($caseId)) {
                    $case_id = (int) $caseId;
                    $case = $this->casesRepository->find($case_id);
                    if ($case) {
                        return $case;
                    }
                }
            }
        }
        return null;
    }

    /**
     * @param string $emailFrom
     * @return Cases|null
     */
    private function getCaseByLastEmail(string $emailFrom): ?Cases
    {
        $clientId = $this->detectClientId($emailFrom);
        if ($clientId !== null && !$case = $this->casesRepository->getByClient($clientId)) {
            $case = $this->casesRepository->getByClientWithAnyStatus($clientId);
        }

        return $case ?? null;
    }

    /**
     * @param string|null $subject
     * @return Lead|null
     */
    private function getLeadFromSubjectByIdOrHash(?string $subject): ?Lead
    {
        if (!$subject) {
            return null;
        }

        preg_match('~\[lid:(\d+)\]~si', $subject, $matches);

        if (!empty($matches[1])) {
            $lead_id = (int) $matches[1];
            $lead = $this->leadRepository->get($lead_id);
        }

        if (empty($lead)) {
            $matches = [];
            preg_match('~\[uid:(\w+)\]~si', $subject, $matches);
            if (!empty($matches[1])) {
                $lead = $this->leadRepository->getByUid((int)$matches[1]);
            }
        }

        return $lead ?? null;
    }

    /**
     * @param string|null $refMessageId
     * @return Lead|null
     */
    private function getLeadFromSubjectByKivTag(?string $refMessageId): ?Lead
    {
        if (!$refMessageId) {
            return null;
        }

        $matches = [];
        preg_match_all('~<kiv\.(.+)>~iU', $refMessageId, $matches);
        if (!empty($matches[1])) {
            foreach ($matches[1] as $messageId) {
                $messageArr = explode('.', $messageId);
                if (!empty($messageArr[2])) {
                    $lead_id = (int) $messageArr[2];

                    $lead = $this->leadRepository->get($lead_id);
                    if ($lead) {
                        return $lead;
                    }
                }
            }
        }
        return null;
    }

    /**
     * @param string $emailFrom
     * @return Lead|null
     */
    private function getLeadByLastEmail(string $emailFrom): ?Lead
    {
        $clientId = $this->detectClientId($emailFrom);
        if ($clientId !== null && !$lead = $this->leadRepository->getActiveByClientId($clientId)) {
            $lead = $this->leadRepository->getByClientId($clientId);
        }

        return $lead ?? null;
    }

    /**
     * @param string $body
     * @return string
     */
    public static function prepareEmailBody(string $body): string
    {
        return str_replace('class="editable"', 'class="editable" contenteditable="true" ', $body);
    }

    /**
     * @param string $email
     * @return bool
     */
    public static function isNotInternalEmail(string $email): bool
    {
        if (UserProjectParams::find()->byEmail($email)->one()) {
            return false;
        }
        if (Employee::find()->byEmail($email)->one()) {
            return false;
        }
        if (DepartmentEmailProject::find()->byEmail($email)->one()) {
            return false;
        }
        return true;
    }


    /**
     * @return array
     */
    public function getUsersIdByEmail(string $email): array
    {
        $users = [];
        $params = UserProjectParams::find()->byEmail($email, false)->all();
        if ($params) {
            foreach ($params as $param) {
                $users[$param->upp_user_id] = $param->upp_user_id;
            }
        }

        $employees = Employee::find()->where(['email' => $email])->all();
        if ($employees) {
            foreach ($employees as $employe) {
                $users[$employe->id] = $employe->id;
            }
        }

        return $users;
    }

    /**
     *
     * @param string $email
     * @return int
     */
    public function getUserIdByEmail(string $email): int
    {
        $user = $this->getUsersIdByEmail($email);
        return reset($user);
    }

    public function getProjectIdByDepOrUpp($emailTo): ?int
    {
        if ($dep = DepartmentEmailProject::find()->byEmail($emailTo)->one()) {
            return $dep->dep_project_id;
        } else if ($upp = UserProjectParams::find()->byEmail($emailTo)->one()) {
            return $upp->upp_project_id;
        }

        return null;
    }
}
