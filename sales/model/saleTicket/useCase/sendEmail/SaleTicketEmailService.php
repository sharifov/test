<?php
namespace sales\model\saleTicket\useCase\sendEmail;

use common\models\CaseSale;
use common\models\Email;
use common\models\Employee;
use common\models\UserProjectParams;
use sales\helpers\cases\CaseSaleHelper;
use sales\model\saleTicket\entity\SaleTicket;
use sales\repositories\cases\CasesSaleRepository;

/**
 * Class SaleTicketEmailService
 * @package sales\model\saleTicket\useCase\sendEmail
 *
 * @property CasesSaleRepository $casesSaleRepository
 */
class SaleTicketEmailService
{
	/**
	 * @var CasesSaleRepository
	 */
	private $casesSaleRepository;

	public function __construct(CasesSaleRepository $casesSaleRepository)
	{
		$this->casesSaleRepository = $casesSaleRepository;
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
		$mail = new Email();
		$mail->e_project_id = $this->getProjectIdFromST($saleTickets[0]);
		$mail->e_case_id = $caseId;
		$mail->e_type_id = Email::TYPE_OUTBOX;
		$mail->e_status_id = Email::STATUS_PENDING;
		$mail->e_email_subject = $this->generateEmailSubject($emailSettings['subject'], $saleTickets[0], $bookingId);
		$mail->body_html = $emailBody;
		$mail->e_email_from = $this->getEmailFrom($mail->e_project_id, $user);


		$mail->e_email_to = $emailSettings['sendTo'][0];
		$mail->e_email_cc = $this->getEmailCC($emailSettings, $saleTickets[0], SaleTicketHelper::isRecallCommissionChanged($saleTickets, $caseSale->getSaleDataDecoded()));
		$mail->e_created_dt = date('Y-m-d H:i:s');
		$mail->e_created_user_id = $user->id;

		if (!$mail->save()) {
			throw new \RuntimeException($mail->getErrorSummary(false)[0]);
		}

		$this->casesSaleRepository->setSendEmailDt(date('Y-m-d H:i:s'), $caseSale);

		$this->sendEmail($mail);
	}

	/**
	 * @param Email $email
	 * @throws \Throwable
	 * @throws \yii\db\StaleObjectException
	 */
	private function sendEmail(Email $email): void
	{
		$email->e_message_id = $email->generateMessageId();
		$email->update();

		$mailResponse = $email->sendMail();
		if (isset($mailResponse['error']) && $mailResponse['error']) {
			throw new \RuntimeException('Error: Email Message has not been sent to ' .  $email->e_email_to);
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