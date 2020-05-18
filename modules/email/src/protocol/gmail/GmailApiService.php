<?php

namespace modules\email\src\protocol\gmail;

use common\components\debug\Logger;
use common\components\debug\Message;
use modules\email\src\protocol\gmail\message\Gmail;
use Google_Client;
use Google_Service_Gmail;
use Google_Service_Gmail_BatchDeleteMessagesRequest;
use Google_Service_Gmail_BatchModifyMessagesRequest;
use Google_Service_Gmail_Message;
use Google_Service_Gmail_ModifyMessageRequest;
use yii\helpers\VarDumper;

/**
 * Class GmailApiService
 *
 * @property Google_Client $client
 * @property Google_Service_Gmail $service
 * @property string $userId
 * @property Logger $logger
 * @property bool $useBatchRequest
 */
class GmailApiService
{
    private const LABEL_UNREAD = 'UNREAD';

    private $client;
    private $service;
    private $userId;
    private $logger;
    private $useBatchRequest;

    public function __construct(Google_Client $client, string  $userId, Logger $logger, bool $useBatchRequest)
    {
        $this->client = $client;
        $this->service = new Google_Service_Gmail($this->client);
        $this->userId = $userId;
        $this->logger = $logger;
        $this->useBatchRequest = $useBatchRequest;
    }

    /**
     * @param array $messagesIds
     * @param  array $optParams
     * @return Gmail[]
     */
    public function getMessages(array $messagesIds, array $optParams = []): array
    {
        if ($this->useBatchRequest) {
            $this->logger->log(Message::info('With batch request'));
            try {
                $messages = $this->getMessagesWithBatchRequest($messagesIds, $optParams);
            } catch (\Throwable $e) {
                $this->logger->log(Message::error('Error: ' . $e->getMessage()));
                self::error(['category' => 'getMessages:BatchRequest', 'error' => $e->getMessage()]);
                $messages = [];
            }
            return $messages;
        }

        $this->logger->log(Message::info('Without batch request'));
        return $this->getMessagesWithoutBatchRequest($messagesIds, $optParams);
    }

    /**
     * @param array $messagesIds
     * @param array  $optParams
     * @return Gmail[]
     */
    public function getMessagesWithoutBatchRequest(array $messagesIds, array $optParams = []): array
    {
        $messages = [];
        $this->logger->messagesInlineMode();
        foreach ($messagesIds as $messageId) {
            $this->logger->log(Message::info('.'));
            if ($message = $this->getMessage($messageId, $optParams)) {
                $this->logger->log(Message::info('+'));
                $messages[] = new Gmail($this->client, $message);
            }
        }
        $this->logger->messagesNewLineMode();
        return $messages;
    }

    /**
     * @param array $messagesIds
     * @param array $optParams
     * @return Gmail[]
     */
    public function getMessagesWithBatchRequest(array $messagesIds, array $optParams = []): array
    {
        $this->client->setUseBatch(true);

        $batch = $this->service->createBatch();

        foreach ($messagesIds as $key => $messageId) {
            if ($message = $this->getMessage($messageId, $optParams)) {
                $batch->add($message, $key);
            }
        }

        $messagesBatch = $batch->execute();

        $this->client->setUseBatch(false);

        $messages = [];

        foreach ($messagesBatch as $message) {
            if ($message instanceof Google_Service_Gmail_Message) {
                $messages[] = new Gmail($this->client, $message);
            } else {
                self::error(['category' => 'Batch request returned error', 'Object returned' => $message]);
            }
        }

        return $messages;
    }

    public  function getMessage(string $messageId, array $optParams = [])
    {
        try {
            return $this->service->users_messages->get($this->userId, $messageId, $optParams);
        } catch (\Throwable $e) {
            $this->logger->log(Message::error('(' . $e->getMessage() . ')'));
            self::error(['category' => 'getMessage', 'messageId' => $messageId, 'error' => $e->getMessage()]);
        }
        return null;
    }

    public function markReadEmails(array $messagesIds): void
    {
        if ($this->useBatchRequest) {
            $this->logger->log(Message::info('With batch request'));
            $this->markReadEmailsWithButchModify($messagesIds);
            return;
        }

        $this->logger->log(Message::info('Without batch request'));
        $this->logger->messagesInlineMode();
        foreach ($messagesIds as $messageId) {
            $this->markReadEmail($messageId);
        }
        $this->logger->messagesNewLineMode();
    }

    public function markReadEmailsWithButchModify(array $messagesIds): void
    {
        $this->batchModifyMessages($messagesIds, [], [self::LABEL_UNREAD]);
    }

    public function batchModifyMessages(array $messagesIds, array $labelsToAdd = [], array $labelsToRemove = [], array $oprParams = []): void
    {
        $mods = new Google_Service_Gmail_BatchModifyMessagesRequest();
        $mods->setIds($messagesIds);
        $mods->setAddLabelIds($labelsToAdd);
        $mods->setRemoveLabelIds($labelsToRemove);
        try {
            $this->service->users_messages->batchModify($this->userId, $mods, $oprParams);
        } catch (\Throwable $e) {
            $this->logger->log(Message::error($e->getMessage()));
            self::error(['category' => 'batchModifyMessages', 'error' => $e->getMessage()]);
        }
    }

    public function markReadEmail(string $messageId): void
    {
        if ($this->modifyMessage($messageId, [], [self::LABEL_UNREAD])) {
            $this->logger->log(Message::success('+'));
        } else {
            $this->logger->log(Message::error('-'));
        }
    }

    public function modifyMessage(string $messageId, array $labelsToAdd = [], array $labelsToRemove = []): ?Google_Service_Gmail_Message
    {
        $mods = new Google_Service_Gmail_ModifyMessageRequest();
        $mods->setAddLabelIds($labelsToAdd);
        $mods->setRemoveLabelIds($labelsToRemove);
        try {
            return $this->service->users_messages->modify($this->userId, $messageId, $mods);
        } catch (\Throwable $e) {
            $this->logger->log(Message::error($e->getMessage()));
            self::error(['category' => 'modifyMessage', 'error' => $e->getMessage()]);
        }
        return null;
    }

    public function deleteEmails(array $messagesIds): void
    {
        if ($this->useBatchRequest) {
            $this->logger->log(Message::info('With batch request'));
            $this->deleteEmailsWithBatchRequest($messagesIds);
            return;
        }

        $this->logger->log(Message::info('Without batch request'));
        $this->logger->messagesInlineMode();
        foreach ($messagesIds as $messageId) {
            $this->deleteEmail($messageId);
        }
        $this->logger->messagesNewLineMode();
    }

    public function deleteEmailsWithBatchRequest(array $messagesIds): void
    {
        $request = new Google_Service_Gmail_BatchDeleteMessagesRequest();
        $request->setIds($messagesIds);
        try {
            $this->service->users_messages->batchDelete($this->userId, $request);
        } catch (\Throwable $e) {
            $this->logger->log(Message::error($e->getMessage()));
            self::error(['category' => 'deleteEmailsWithBatchRequest', 'error' => $e->getMessage()]);
        }
    }

    public function deleteEmail(string $messageId): void
    {
        try {
            $this->service->users_messages->delete($this->userId, $messageId);
            $this->logger->log(Message::success('+'));
        } catch (\Throwable $e) {
            $this->logger->log(Message::error('(' . $e->getMessage() . ')'));
            self::error(['category' => 'deleteEmail', 'messageId' => $messageId, 'error' => $e->getMessage()]);
        }
    }

    /**
     * @param GmailCriteria $criteria
     * @return Google_Service_Gmail_Message[] array
     */
    public function getListMessages(GmailCriteria $criteria): array
    {
        try {
            return ($this->getMessagesResponse($criteria->getOptParams()))->getMessages();
        } catch (\Throwable $e) {
            $this->logger->log(Message::error($e->getMessage()));
            self::error(['category' => 'getListMessages', 'error' => $e->getMessage()]);
        }
        return [];
    }

    /**
     * @param array $params
     *
     * @return \Google_Service_Gmail_ListMessagesResponse|object
     * @throws \Google_Exception
     */
    public function getMessagesResponse(array $params)
    {
        $responseOrRequest = $this->service->users_messages->listUsersMessages($this->userId, $params);

        if (get_class($responseOrRequest) === "GuzzleHttp\Psr7\Request") {
            $response = $this->service->getClient()->execute($responseOrRequest, 'Google_Service_Gmail_ListMessagesResponse');
            return $response;
        }

        return $responseOrRequest;
    }

    private static function error($var): void
    {
        \Yii::error(VarDumper::dumpAsString($var), 'GmailApiService');
    }
}
