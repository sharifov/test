<?php

namespace modules\mail\src\gmail;

use common\components\debug\Logger;
use common\components\debug\Message;
use modules\mail\src\gmail\message\Gmail;
use common\components\mail\MailHelper;
use common\components\mail\MailProjects;
use common\components\mail\Result;
use common\models\EmailAccount;
use common\models\EmailIncoming;
use Google_Service_Gmail_Message;
use yii\helpers\VarDumper;

/**
 * Class Gmail
 *
 * @property GmailApiService $api
 * @property MailProjects $projects
 * @property EmailAccount $emailAccount
 * @property int|null $lastEmailSavedId
 * @property array $processedEmails
 * @property array $savedEmails
 * @property array $existEmails
 * @property array $debugEmails
 * @property Logger $logger
 */
class GmailService
{
    public const COMMAND_DELETE = 'delete';
    public const COMMAND_MARK_READ = 'read';
    public const COMMAND_NO_ACTION = 'no-action';

    public const COMMAND_LIST = [
        self::COMMAND_DELETE => self::COMMAND_DELETE,
        self::COMMAND_MARK_READ => self::COMMAND_MARK_READ,
        self::COMMAND_NO_ACTION => self::COMMAND_NO_ACTION,
    ];

    private $api;
    private $projects;
    private $emailAccount;
    private $lastEmailSavedId;
    private $processedEmails = [];
    private $savedEmails = [];
    private $existEmails = [];
    private $debugEmails = [];
    private $logger;

    public function __construct(GmailApiService $api, EmailAccount $account, array $projectList, Logger $logger)
    {
        $this->api = $api;
        $this->emailAccount = $account;
        $this->projects = new MailProjects($projectList);
        $this->logger = $logger;
    }

    /**
     * @param string $command 'read', 'delete'
     * @param GmailCriteria $criteria
     * @return Result
     */
    public function downloadMessages(string $command, GmailCriteria $criteria): Result
    {
        $this->logger->timerStart('download_messages')->log(Message::info('GmailService downloadMessages Start'));
        $this->logger->log(Message::success(
            'Account Id: "' . $this->emailAccount->ea_id
            . '" Account Username: "' . $this->emailAccount->ea_username
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
            return new Result(0);
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

        return new Result($this->getLastEmailSavedId(), $this->projects->getProjectIds());
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

            if ($updEmailIncoming = $this->findEmailIncoming($gmail, $emailTo)) {
                $this->updateEmail($gmail, $updEmailIncoming);
                $this->addExistEmail($gmail->getId());
                continue;
            }

            if (!$projectId = $this->projects->getProjectId($emailTo)) {
                $this->logger->log(Message::warning('(EmailToHost for emailTo: "' . $emailTo . '" not found in Projects)'));
            }

            $eIncoming = new EmailIncoming();
            $eIncoming->ei_deleted = false;
            $eIncoming->ei_email_from = $gmail->getFromEmail();
            $eIncoming->ei_email_from_name = MailHelper::cleanText($gmail->getFromName());
            $eIncoming->ei_email_subject = MailHelper::cleanText($gmail->getSubject());
            $eIncoming->ei_email_to = $emailTo;
            $eIncoming->ei_project_id = $projectId;
            $eIncoming->ei_email_text = $emailText;
            $eIncoming->ei_account_id = $this->emailAccount->ea_id;
            $eIncoming->ei_ref_mess_ids = $gmail->getReferences();
            $eIncoming->ei_message_id = $messageId;
            $eIncoming->ei_received_dt = $gmail->getDate();

            if ($eIncoming->save()) {
                $this->logger->log(Message::info('+'));
                $this->projects->addProjectId($projectId);
                $this->lastEmailSavedId = $eIncoming->ei_id;
                $this->addSavedEmail($gmail->getId());
            } else {
                $savedError = true;
                $this->logger->log(Message::error('(' . VarDumper::dumpAsString($eIncoming->getErrors()) . ')'));
                self::error(['category' => 'saveMessage', 'messageId' => $gmail->getId(), 'model' => $eIncoming->getAttributes(), 'error' => $eIncoming->getErrors()]);
            }

        }
        if ($savedError === false) {
            $this->addProcessedEmail($gmail->getId());
        }

    }

    private function processProcessedEmails(string $command, array $processedEmailsIds): void
    {
        if (!$processedEmailsIds) {
            return;
        }

        if ($command === self::COMMAND_DELETE) {
            $this->logger->timerStart('delete')->log(Message::info('Start delete'));
            $this->api->deleteEmails($processedEmailsIds);
            $this->logger->timerStop('delete')->log(Message::info('Finish delete'));
            return;
        }
        if ($command === self::COMMAND_MARK_READ) {
            $this->logger->timerStart('read')->log(Message::info('Start mark read'));
            $this->api->markReadEmails($processedEmailsIds);
            $this->logger->timerStop('read')->log(Message::info('Finish mark read'));
            return;
        }
        if ($command === self::COMMAND_NO_ACTION) {
            $this->logger->log(Message::info('No actions'));
            return;
        }

        $this->logger->log(Message::error('Undefined command: ' . $command));
        self::error(['category' => 'processSavedMessages', 'error' => 'Undefined command: ' . $command]);
    }

    private function updateEmail(Gmail $gmail, EmailIncoming $existEmail): void
    {
        $receivedDtChanged = false;
        if ($existEmail->ei_received_dt !== $gmail->getDate()) {
            $receivedDtChanged = true;
            $existEmail->ei_received_dt = $gmail->getDate();
        }
        $existEmail->ei_updated_dt = date('Y-m-d H:i:s');
        if ($existEmail->save()) {
            $this->logger->log(Message::warning(
                '(Email Id: "' . $existEmail->ei_id
                . '" Gmail Id: "' . $gmail->getId() . '" Gmail Message-Id: "' . $gmail->getMessageId() . '" already exist.'
                . ($receivedDtChanged ? ' Received date was updated' : '') . ')'
            ));
        } else {
            self::error(['category' => 'updateEmail', 'error' => $existEmail->getErrors(), 'model' => $existEmail->getAttributes()]);
            $this->logger->log(Message::error('(' . VarDumper::dumpAsString($existEmail->getErrors()) . ')'));
        }
//        $this->addDebugEmail(
//            $gmail->getId(), 'Already_exist', [
//                'old time created' => $existEmail->ei_created_dt,
//                'now time' => date('Y-m-d H:i:s'),
//                'exist model' => $existEmail->getAttributes(),
//                'gmail' => $gmail->toArray()
//        ]);
    }

    private function findEmailIncoming(Gmail $gmail, string $emailTo): ?EmailIncoming
    {
        return EmailIncoming::find()->where(['ei_message_id' => $gmail->getMessageId(), 'ei_email_to' => $emailTo])->one();
    }

    private function getLastEmailSavedId(): ?int
    {
        return $this->lastEmailSavedId;
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
