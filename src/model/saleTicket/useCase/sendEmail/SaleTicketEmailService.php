<?php

namespace src\model\saleTicket\useCase\sendEmail;

use common\models\CaseSale;
use common\models\Email;
use common\models\Employee;
use common\models\UserProjectParams;
use src\model\saleTicket\entity\SaleTicket;
use src\repositories\cases\CasesSaleRepository;
use src\dto\email\EmailDTO;
use src\services\email\EmailMainService;
use src\exception\CreateModelException;
use src\exception\EmailNotSentException;

/**
 * Class SaleTicketEmailService
 * @package src\model\saleTicket\useCase\sendEmail
 *
 * @property CasesSaleRepository $casesSaleRepository
 * @property EmailMainService $emailService
 */
class SaleTicketEmailService
{
    /**
     * @var CasesSaleRepository
     */
    private $casesSaleRepository;
    /**
     * @var EmailMainService
     */
    private $emailService;

    public function __construct(CasesSaleRepository $casesSaleRepository, EmailMainService $emailService)
    {
        $this->casesSaleRepository = $casesSaleRepository;
        $this->emailService = $emailService;
    }

    /**
     * @param SaleTicket[] $saleTickets
     * @param array $emailSettings
     * @param string $emailBody
     * @param int $caseId
     * @param string $bookingId
     * @param Employee $user
     * @param array $caseSaleData
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function generateAndSendEmail(array $saleTickets, array $emailSettings, string $emailBody, int $caseId, string $bookingId, Employee $user, CaseSale $caseSale): void
    {
        try {
            $emailDTO = EmailDTO::fromArray([
                'projectId' => $this->getProjectIdFromST($saleTickets[0]),
                'caseId' => $caseId,
                'emailSubject' => $this->generateEmailSubject($emailSettings['subject'], $saleTickets[0], $bookingId),
                'bodyHtml' => $emailBody,
                'emailFrom' => $this->getEmailFrom($caseSale->cssCs->cs_project_id ?? null, $user),
                'emailTo' => $emailSettings['sendTo'][0],
                'createdUserId' => $user->id,
                'emailCc' => $this->getEmailCC($emailSettings, $saleTickets[0], SaleTicketHelper::isRecallCommissionChanged($saleTickets, $caseSale->getSaleDataDecoded()))
            ]);

            $mail = $this->emailService->createFromDTO($emailDTO, false);

            $this->casesSaleRepository->setSendEmailDt(date('Y-m-d H:i:s'), $caseSale);

            $this->emailService->sendMail($mail);
        } catch (CreateModelException $e) {
            throw new \RuntimeException($e->getErrorSummary(false)[0]);
        } catch (EmailNotSentException $e) {
            throw new \RuntimeException('Error: Email Message has not been sent to ' .  $mail->getEmailTo(false));
        }
    }

    /**
     * @param array $emailSettings
     * @param SaleTicket $saleTicket
     * @param bool $isChangedRecallCommission
     * @return string
     */
    public function getEmailCC(array $emailSettings, SaleTicket $saleTicket, bool $isChangedRecallCommission): string
    {
        $emails = $emailSettings['sendTo'] ?? [];
        $bookeepingEmails = $emailSettings['bookeepingEmails'] ?? [];
        $additionalEmail = $emailSettings['emailOnRecallCommChanged'] ?? [];

        $emails = $saleTicket->isNeedAdditionalInfoForEmail() ? array_merge($emails, $bookeepingEmails) : $emails;

        if ($isChangedRecallCommission) {
            $emails = array_merge($emails, $additionalEmail);
        }

        if (!empty($emails)) {
            unset($emails[0]);
            return implode(',', $emails);
        }
        return '';
    }

    /**
     * @param SaleTicket $saleTicket
     * @return int|null
     */
    private function getProjectIdFromST(SaleTicket $saleTicket): ?int
    {
        return $saleTicket->stCase ? $saleTicket->stCase->cs_project_id : null;
    }

    /**
     * @param string $subject
     * @param SaleTicket $saleTicket
     * @param string $bookingId
     * @return string|string[]|null
     */
    private function generateEmailSubject(string $subject, SaleTicket $saleTicket, string $bookingId)
    {
        return preg_replace(['/\[bookingId\]/', '/\[originalFop\]/'], [$bookingId, $saleTicket->getFormattedOriginalFop()], $subject);
    }

    private function getEmailFrom(?int $projectId, Employee $user)
    {
        $emailFrom = $user->email;
        if ($projectId) {
            $upp = UserProjectParams::find()->where(['upp_project_id' => $projectId, 'upp_user_id' => $user->id])->withEmailList()->one();
            if ($upp) {
                $emailFrom = $upp->getEmail() ?: $emailFrom;
            }
        }

        if (!$emailFrom) {
            throw new \RuntimeException('Agent not has assigned email');
        }

        return $emailFrom;
    }
}
