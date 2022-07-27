<?php

namespace modules\email\src\protocol\gmail;

use common\components\debug\Logger;
use common\components\debug\Message;
use common\components\jobs\CreateSaleFromBOJob;
use common\models\Email;
use Google_Service_Gmail_Message;
use modules\email\src\entity\emailAccount\EmailAccount;
use modules\email\src\helpers\MailHelper;
use modules\email\src\Projects;
use modules\email\src\protocol\gmail\message\Gmail;
use modules\email\src\Result;
use src\helpers\app\AppHelper;
use src\services\cases\CasesManageService;
use src\services\email\EmailService;
use src\services\email\incoming\EmailIncomingService;
use Yii;
use yii\helpers\VarDumper;
use src\services\email\EmailMainService;
use src\dto\email\EmailDTO;
use src\exception\CreateModelException;
use src\services\email\EmailServiceHelper;
use src\repositories\email\EmailRepositoryFactory;

/**
 * Class Gmail
 *
 * @property GmailApiService $api
 * @property EmailAccount $emailAccount
 * @property int|null $lastEmailSavedId
 * @property array $processedEmails
 * @property array $savedEmails
 * @property array $existEmails
 * @property array $debugEmails
 * @property array $emailsTo
 * @property Logger $logger
 * @property EmailMainService $emailService
 * @property Projects $projects
 * @property array $usersForNotification
 */
class GmailService
{
    private $api;
    private $emailAccount;
    private $processedEmails = [];
    private $savedEmails = [];
    private $existEmails = [];
    private $debugEmails = [];
    private $emailsTo = [];
    private $logger;
    private $emailService;
    private $projects;
    private $usersForNotification = [];

    public function __construct(
        GmailApiService $api,
        EmailAccount $account,
        Logger $logger,
        EmailMainService $emailService,
        Projects $projects
    ) {
        $this->api = $api;
        $this->emailAccount = $account;
        $this->logger = $logger;
        $this->emailService = $emailService;
        $this->projects = $projects;
    }

    /**
     * @param string $command 'read', 'delete', 'no-action'
     * @param GmailCriteria $criteria
     * @return Result
     */
    public function downloadMessages(string $command, GmailCriteria $criteria): Result
    {
        $this->logger->timerStart('download_messages')->log(Message::info('GmailService downloadMessages Start'));
        $this->logger->log(Message::success(
            'Account Id: "' . $this->emailAccount->ea_id
            . '" Account email: "' . $this->emailAccount->ea_email
            . '" Criteria: "' . $criteria->toString() . '"'
        ));

        $this->logger->timerStart('get_list_messages')->log(Message::info('Start get list messages'));
        $listMessages = $this->api->getListMessages($criteria);
        $this->logger->timerStop('get_list_messages')->log(Message::info('Finish get list messages'));

        $count = count($listMessages);

        $this->logger->log(Message::info('COUNT: ' . $count));

        if (!$count) {
            $this->logger->log(Message::info('GmailService downloadMessages Finish'));
            return new Result([]);
        }

        $this->logger->timerStart('get_messages')->log(Message::info('Start get messages'));
        $messages = $this->api->getMessages($this->listMessagesToMessagesIds($listMessages));
        $this->logger->log(Message::info('Got "' . count($messages) . '" messages'));
        $this->logger->timerStop('get_messages')->log(Message::info('Finish get messages'));

        $this->logger->timerStart('save_messages')->log(Message::info('Start save messages'));
        $this->saveMessages(...$messages);
        $this->logger->timerStop('save_messages')->log(Message::info('Finish save messages'));

        $this->processDebugEmails();

        $this->logger->timerStart('processing_gmail_message')->log(Message::info('Start processing gmail messages'));
        $this->processProcessedEmails($command, array_keys($this->processedEmails));
        $this->logger->timerStop('processing_gmail_message')->log(Message::info('Finish processing gmail messages'));

        $this->logger->log(Message::success('Processed emails: ' . count($this->processedEmails)));
        $this->logger->log(Message::success('Saved emails: ' . count($this->savedEmails)));
        $this->logger->log(Message::success('Exist emails: ' . count($this->existEmails)));
        $this->logger->log(Message::success('Debug emails: ' . count($this->debugEmails)));
        $this->logger->timerStop('download_messages')->log(Message::info('GmailService downloadMessages Finish'));

        return new Result($this->emailsTo);
    }

    private function saveMessages(Gmail ...$messages): void
    {
        $this->logger->messagesInlineMode();
        foreach ($messages as $message) {
            $this->logger->log(Message::info('*'));
            try {
                $this->saveMessage($message);
            } catch (\Throwable $e) {
                $this->logger->log(Message::error('(' . $e->getMessage() . ')'));
                self::error(['category' => 'saveMessages', 'messageId' => $message->getId(), 'error' => $e]);
            }
        }
        $this->logger->messagesNewLineMode();
    }

    /**
     * @param Google_Service_Gmail_Message[] $listMessages
     * @return array
     */
    private function listMessagesToMessagesIds(array $listMessages): array
    {
        $messageIds = [];
        foreach ($listMessages as $listMessage) {
            $messageIds[] = $listMessage->getId();
        }
        return $messageIds;
    }

    private function saveMessage(Gmail $gmail): void
    {
        if (!$messageId = $gmail->getMessageId()) {
            $error = 'Not found Message Id from headers email for email Id: "' . $gmail->getId() . '"';
            self::error(['category' => 'Email Message Id', 'error' => $error]);
            $this->logger->log(Message::error('(' . $error . ')'));
//            $this->addProcessedEmail($gmail->getId());
            $this->addDebugEmail($gmail->getId(), 'Not_found_Message_Id');
            return;
        }

        if (MailHelper::isExistInBlackList($gmail->getFromEmail())) {
            $this->logger->log(Message::info('(Message Id: "' . $gmail->getId() . '". From email: "' . $gmail->getFromEmail() . '" in black list)'));
            $this->addProcessedEmail($gmail->getId());
            return;
        }

        $emailText = MailHelper::cleanText($gmail->getContent());

        if (strip_tags($emailText) === '') {
            $this->logger->log(Message::info('(Message Id: "' . $gmail->getId() . '" email text is empty)'));
            $this->addProcessedEmail($gmail->getId());
//            $this->addDebugEmail($gmail->getId(), 'Email_text_is_empty');
            return;
        }

        if (!$emailsTo = $gmail->getToWithCc()) {
            self::error(['category' => 'Email "To"', 'message' => 'Message Id: "' . $gmail->getId() . '". Not found "To"']);
            $this->logger->log(Message::error('(Message Id: "' . $gmail->getId() . '". Not found "To")'));
//            $this->addProcessedEmail($gmail->getId());
            $this->addDebugEmail($gmail->getId(), 'Not_found_To');
            return;
        }

        $savedError = false;
        foreach ($emailsTo as $emailTo) {
            $this->logger->log(Message::info('.'));

            if ($this->emailExist($messageId, $emailTo)) {
                $this->addExistEmail($gmail->getId());
                continue;
            }

            try {
                $emailDTO = EmailDTO::newInstance()->fillFromGmail($gmail, $emailTo);
                $this->emailService->receiveEmail($emailDTO);
                $users = (Yii::createObject(EmailServiceHelper::class))->getUsersIdByEmail($emailTo);
            } catch (\Throwable $e) {
                $error = $e->getMessage();
                if ($e instanceof CreateModelException) {
                    $error = $e->getErrors();
                }
                $savedError = true;
                $this->logger->log(Message::error('(' . VarDumper::dumpAsString($error) . ')'));
                self::error(['category' => 'saveMessage', 'messageId' => $gmail->getId(), 'error' => $error]);
                continue;
            }

            $this->logger->log(Message::info('+'));
            $this->addSavedEmail($gmail->getId());
            $this->addUsersForNotification($users);
        }

        if ($savedError === false) {
            $this->addProcessedEmail($gmail->getId());
        }
    }

    private function processEmail(Email $email): void
    {
        if ($email->e_lead_id === null && $email->e_case_id === null && EmailServiceHelper::isNotInternalEmail($email->e_email_from)) {
            try {
                $process = (Yii::createObject(EmailIncomingService::class))->create(
                    $email->e_id,
                    $email->e_email_from,
                    $email->e_email_to,
                    $email->e_project_id
                );
                $email->e_lead_id = $process->leadId;
                $email->e_case_id = $process->caseId;
                $email->save(false);
            } catch (\DomainException $e) {
                Yii::info(['category' => 'processEmail', 'error' => $e->getMessage()]);
            } catch (\Throwable $e) {
                self::error(['category' => 'processEmail', 'error' => $e->getMessage()]);
            }
        }

        if ($email->e_case_id) {
            try {
                (Yii::createObject(CasesManageService::class))->needAction($email->e_case_id);
                $job = new CreateSaleFromBOJob();
                $job->case_id = $email->e_case_id;
                $job->email = $email->e_email_from;
                $job->project_key = $email->eProject->api_key ?? null;
                Yii::$app->queue_job->priority(100)->push($job);
            } catch (\Throwable $throwable) {
                self::error(['category' => 'addToJobFailed', 'error' => AppHelper::throwableFormatter($throwable)]);
            }
        }
    }

    private function processProcessedEmails(string $command, array $processedEmailsIds): void
    {
        if (!$processedEmailsIds) {
            return;
        }

        if ($command === EmailAccount::GMAIL_COMMAND_DELETE) {
            $this->logger->timerStart('delete')->log(Message::info('Start delete'));
            $this->api->deleteEmails($processedEmailsIds);
            $this->logger->timerStop('delete')->log(Message::info('Finish delete'));
            return;
        }
        if ($command === EmailAccount::GMAIL_COMMAND_MARK_READ) {
            $this->logger->timerStart('read')->log(Message::info('Start mark read'));
            $this->api->markReadEmails($processedEmailsIds);
            $this->logger->timerStop('read')->log(Message::info('Finish mark read'));
            return;
        }
        if ($command === EmailAccount::GMAIL_COMMAND_NO_ACTION) {
            $this->logger->log(Message::info('No actions'));
            return;
        }

        $this->logger->log(Message::error('Undefined command: ' . $command));
        self::error(['category' => 'processSavedMessages', 'error' => 'Undefined command: ' . $command]);
    }

    private function emailExist(string $messageId, string $emailTo): bool
    {
        return EmailRepositoryFactory::getRepository()->findReceived($messageId, $emailTo)->exists();
    }

    private function addProcessedEmail(string $id): void
    {
        $this->processedEmails[$id] = $id;
    }

    private function addSavedEmail(string $id): void
    {
        $this->savedEmails[] = $id;
    }

    private function addExistEmail(string $id): void
    {
        $this->existEmails[] = $id;
    }

    private function addEmailTo(string $id): void
    {
        $this->emailsTo[] = $id;
    }

    private function addUsersForNotification(array $users): void
    {
        foreach ($users as $user) {
            $this->usersForNotification[$user] = $user;
        }
    }

    private function addDebugEmail(string $id, string $category, $message = ''): void
    {
        $this->debugEmails[$id] = [
            'id' => $id,
            'category' => $category,
            'message' => $message,
        ];
    }

    private function processDebugEmails(): void
    {
        if ($debugEmailsIds = array_keys($this->debugEmails)) {
            $this->logger->timerStart('process_debug_emails')->log(Message::info('Start process debug emails'));
            foreach ($this->api->getMessages($debugEmailsIds, ['format' => 'raw']) as $debugEmail) {
                \Yii::warning(VarDumper::dumpAsString([
                    'category' => $this->debugEmails[$debugEmail->getId()]['category'],
                    'message' => $this->debugEmails[$debugEmail->getId()]['message'],
                    'raw' => $debugEmail->getRaw()
                ]), 'info\GmailService:debugEmail:' . $this->debugEmails[$debugEmail->getId()]['category']);
            }
            $this->logger->timerStop('process_debug_emails')->log(Message::info('Finish process debug emails'));
        }
    }

    private static function error($var): void
    {
        \Yii::error(VarDumper::dumpAsString($var), 'GmailService');
    }
}
