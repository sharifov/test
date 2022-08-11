<?php

namespace console\controllers;

use common\models\Email;
use common\models\Log;
use modules\fileStorage\src\entity\fileCase\FileCase;
use modules\fileStorage\src\entity\fileCase\FileCaseRepository;
use modules\fileStorage\src\entity\fileClient\FileClient;
use modules\fileStorage\src\entity\fileClient\FileClientRepository;
use modules\fileStorage\src\entity\fileLead\FileLead;
use modules\fileStorage\src\entity\fileLead\FileLeadRepository;
use modules\fileStorage\src\entity\fileStorage\FileStorage;
use modules\fileStorage\src\entity\fileStorage\FileStorageRepository;
use modules\fileStorage\src\FileSystem;
use modules\fileStorage\src\services\CreateByApiDto;
use modules\fileStorage\src\services\url\UrlGenerator;
use yii\console\Controller;
use yii\helpers\ArrayHelper;

/**
 * Class FixController
 *
 * @property FileSystem $fileSystem
 * @property UrlGenerator $urlGenerator
 * @property FileStorageRepository $fileStorageRepository
 * @property FileClientRepository $fileClientRepository
 * @property FileCaseRepository $fileCaseRepository
 * @property FileLeadRepository $fileLeadRepository
 */
class FixController extends Controller
{
    private FileSystem $fileSystem;
    private UrlGenerator $urlGenerator;
    private FileStorageRepository $fileStorageRepository;
    private FileClientRepository $fileClientRepository;
    private FileCaseRepository $fileCaseRepository;
    private FileLeadRepository $fileLeadRepository;

    public function __construct(
        $id,
        $module,
        FileSystem $fileSystem,
        UrlGenerator $urlGenerator,
        FileStorageRepository $fileStorageRepository,
        FileClientRepository $fileClientRepository,
        FileCaseRepository $fileCaseRepository,
        FileLeadRepository $fileLeadRepository,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->fileSystem = $fileSystem;
        $this->urlGenerator = $urlGenerator;
        $this->fileStorageRepository = $fileStorageRepository;
        $this->fileClientRepository = $fileClientRepository;
        $this->fileCaseRepository = $fileCaseRepository;
        $this->fileLeadRepository = $fileLeadRepository;
    }

    public function actionGetEmailWithAttachemntError()
    {
        $errorCategory = 'DownloadEmails:Attach:fileExists';
        $systemLogs = Log::find()->select(['message'])->andWhere(['category' => 'DownloadEmails:Attach:fileExists'])->column();
        $logs[] = 'Found ' . count($systemLogs) . ' system logs with ' . $errorCategory . ' category';

        $communicationIds = [];
        foreach ($systemLogs as $systemLog) {
            $commId = $this->getCommunicationIdFromLog($systemLog);
            if ($commId) {
                $communicationIds[$commId] = $commId;
            }
        }

        if (empty($communicationIds)) {
            $logs[] = 'Communication Ids not found in systemLogs with category: ' . $errorCategory;
            $this->exportLogs($logs);
            return;
        }

        $communicationIds = array_keys($communicationIds);

        $logs[] = 'Found ' . count($communicationIds) . ' communication Ids in systemLogs with category: ' . $errorCategory;
        $logs[] = 'Communication Ids: ' . implode(', ', $communicationIds);

        $emails = Email::find()->select([
            'e_id',
            'e_email_data',
            'e_inbox_email_id',
            'e_client_id',
            'e_lead_id',
            'e_case_id',
        ])->andWhere(['e_inbox_email_id' => $communicationIds])->asArray()->all();

        if (empty($emails)) {
            $logs[] = 'Not found emails with communication Ids';
            $this->exportLogs($logs);
            return;
        }

        $logs[] = 'Found ' . count($emails) . ' emails';
        $logs[] = 'Email Ids: ' . implode(', ', ArrayHelper::getColumn($emails, 'e_id'));

        $this->exportLogs($logs);
    }

    public function actionRestoreEmailAttachmentRelation()
    {
        $errorCategory = 'DownloadEmails:Attach:fileExists';

        $logs[] = 'Start ' . date('Y-m-d H:i:s') . ' restore email attachments';
        $systemLogs = Log::find()->select(['message'])->andWhere(['category' => 'DownloadEmails:Attach:fileExists'])->column();
        $logs[] = 'Found ' . count($systemLogs) . ' system logs with ' . $errorCategory . ' category';

        $communicationIds = [];
        foreach ($systemLogs as $systemLog) {
            $commId = $this->getCommunicationIdFromLog($systemLog);
            if ($commId) {
                $communicationIds[$commId] = $commId;
            }
        }

        if (empty($communicationIds)) {
            $logs[] = 'Communication Ids not found in systemLogs with category: ' . $errorCategory;
            $this->exportLogs($logs);
            return;
        }

        $communicationIds = array_keys($communicationIds);

        $logs[] = 'Found ' . count($communicationIds) . ' communication Ids in systemLogs with category: ' . $errorCategory;
        $logs[] = 'Communication Ids: ' . implode(', ', $communicationIds);

        $emails = Email::find()->select([
            'e_id',
            'e_email_data',
            'e_inbox_email_id',
            'e_client_id',
            'e_lead_id',
            'e_case_id',
        ])->andWhere(['e_inbox_email_id' => $communicationIds])->asArray()->all();

        if (empty($emails)) {
            $logs[] = 'Not found emails with communication Ids';
            $this->exportLogs($logs);
            return;
        }

        $logs[] = 'Found ' . count($emails) . ' emails';
        $logs[] = 'Email Ids: ' . implode(', ', ArrayHelper::getColumn($emails, 'e_id'));

        foreach ($emails as $email) {
            $attachments = \Yii::$app->comms->getEmailAttachments($email['e_inbox_email_id']);
            if (empty($attachments)) {
                $logs[] = 'Not found attachments for Email Id: ' . $email['e_id'];
                continue;
            }
            try {
                $this->attachFilesToEmail($attachments, $email, $logs);
            } catch (\Throwable $e) {
                $logs[] = 'Error attach files to Email Id: ' . $email['e_id'] . ' Message: ' . $e->getMessage();
            }
        }

        $logs[] = 'Finish ' . date('Y-m-d H:i:s') . ' restore email attachments';
        $this->exportLogs($logs);
    }

    private function attachFilesToEmail(array $attachments, array $email, &$logs): void
    {
        $emailDataAttachments = [];
        foreach ($attachments as $path) {
            $fileExist = $this->fileSystem->fileExists($path);
            if ($fileExist) {
                $createByApiDto = CreateByApiDto::createWithFile($path, $this->fileSystem);
            } else {
                $createByApiDto = CreateByApiDto::createWithoutFile($path);
                $logs[] = 'File Not exist in storage: ' . $path . ' | EmailId: ' . $email['e_id'];
            }

            $fileStorage = FileStorage::createByEmail($createByApiDto);
            $this->fileStorageRepository->save($fileStorage);

            $emailDataAttachments['files'][] = [
                'value' => $fileStorage->fs_path,
                'name' => $fileStorage->fs_name,
                'type_id' => $fileStorage->fs_private ? $this->urlGenerator::TYPE_PRIVATE : $this->urlGenerator::TYPE_PUBLIC,
            ];

            if ($email['e_client_id'] && $fileStorage->fs_id) {
                $fileClient = FileClient::create($fileStorage->fs_id, $email['e_client_id']);
                $this->fileClientRepository->save($fileClient);
            }
            if ($email['e_case_id'] && $fileStorage->fs_id) {
                $fileCase = FileCase::create($fileStorage->fs_id, $email['e_case_id']);
                $this->fileCaseRepository->save($fileCase);
            }
            if ($email['e_lead_id'] && $fileStorage->fs_id) {
                $fileLead = FileLead::create($fileStorage->fs_id, $email['e_lead_id']);
                $this->fileLeadRepository->save($fileLead);
            }
        }
        if ($emailDataAttachments) {
            $update = Email::updateAll(['e_email_data' => json_encode($emailDataAttachments)], ['e_id' => $email['e_id']]);
            if (!$update) {
                $logs[] = 'Attachments data not saved. Email Id: ' . $email['e_id'];
            }
        }
    }

    private function getCommunicationIdFromLog($log): ?int
    {
        $pattern = '/\'communicationId\' => (.*)\n/';
        preg_match($pattern, $log, $matches);
        if (!empty($matches[1])) {
            return $matches[1];
        }
        return null;
    }

    private function exportLogs(array $logs): void
    {
        \Yii::info($logs, 'info\RestoreEmailAttachments');
    }
}
