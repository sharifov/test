<?php

namespace src\services\email;

use common\models\ClientEmail;
use common\models\DepartmentEmailProject;
use common\models\Email;
use common\models\Employee;
use common\models\Lead;
use common\models\UserProjectParams;
use src\entities\cases\Cases;
use src\repositories\cases\CasesRepository;
use src\repositories\lead\LeadRepository;
use src\repositories\NotFoundException;
use Yii;

/**
 * Class EmailService
 *
 * @property LeadRepository $leadRepository
 * @property CasesRepository $casesRepository
 */
class EmailService
{
    /**
     * @var LeadRepository
     */
    private $leadRepository;

    /**
     * @var CasesRepository
     */
    private $casesRepository;

    /**
     * EmailService constructor.
     * @param LeadRepository $leadRepository
     * @param CasesRepository $casesRepository
     */
    public function __construct(LeadRepository $leadRepository, CasesRepository $casesRepository)
    {
        $this->leadRepository = $leadRepository;
        $this->casesRepository = $casesRepository;
    }

    /**
     * @param Email $email
     * @return int|null
     */
    public function detectLeadId(Email $email): ?int
    {
        $subject = $email->e_email_subject;

        try {
            $lead = $this->getLeadFromSubjectByIdOrHash($subject);

            if (!$lead && !$lead = $this->getLeadFromSubjectByKivTag($email->e_ref_message_id)) {
//              $lead = $this->getLeadByLastEmail($email->e_email_from);
            }
        } catch (NotFoundException $exception) {
            Yii::info('(' . $exception->getCode() . ') ' . $exception->getMessage() . ' File: ' . $exception->getFile() . '(Line: ' . $exception->getLine() . ')' . '; message_id: ' . $email->e_ref_message_id, 'info\SalesEmailService:detectLeadId:NotFoundException');
        }

        $email->e_lead_id = $lead->id ?? null;

        return $email->e_lead_id;
    }

    /**
     * @param Email $email
     * @return int|null
     */
    public function detectCaseId(Email $email): ?int
    {
        $subject = $email->e_email_subject;

        try {
            $case = $this->getCaseFromSubjectByIdOrHash($subject);

            if (!$case && !$case = $this->getCaseFromSubjectByKivTag($email->e_ref_message_id)) {
//              $case = $this->getCaseByLastEmail($email->e_email_from);
            }
        } catch (NotFoundException $exception) {
            Yii::info('(' . $exception->getCode() . ') ' . $exception->getMessage() . ' File: ' . $exception->getFile() . '(Line: ' . $exception->getLine() . ')' . '; message_id: ' . $email->e_ref_message_id, 'info\SalesEmailService:detectCaseId:NotFoundException');
        }

        $email->e_case_id = $case->cs_id ?? null;

        return $email->e_case_id;
    }

    /**
     * @param string $email
     * @return bool
     */
    public function isNotInternalEmail(string $email): bool
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
        $clientEmail = ClientEmail::find()->byEmail($emailFrom)->one();
        if (
            $clientEmail &&
            $clientEmail->client_id &&
            !$case = $this->casesRepository->getByClient($clientEmail->client_id)
        ) {
            $case = $this->casesRepository->getByClientWithAnyStatus($clientEmail->client_id);
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
        $clientEmail = ClientEmail::find()->byEmail($emailFrom)->one();
        if (
            $clientEmail &&
            $clientEmail->client_id &&
            !$lead = $this->leadRepository->getActiveByClientId($clientEmail->client_id)
        ) {
            $lead = $this->leadRepository->getByClientId($clientEmail->client_id);
        }

        return $lead ?? null;
    }

    public function detectClientId(string $email)
    {
        $clientEmail = ClientEmail::find()->byEmail($email)->one();

        return $clientEmail->client_id ?? null;
    }

    /**
     * @param string $body
     * @return string
     */
    public static function prepareEmailBody(string $body): string
    {
        return str_replace('class="editable"', 'class="editable" contenteditable="true" ', $body);
    }
}
