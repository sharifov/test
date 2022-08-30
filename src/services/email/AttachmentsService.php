<?php

namespace src\services\email;

use src\helpers\app\AppHelper;
use Yii;
use modules\fileStorage\src\entity\fileStorage\FileStorageRepository;
use modules\fileStorage\src\entity\fileClient\FileClientRepository;
use modules\fileStorage\src\entity\fileLead\FileLeadRepository;
use modules\fileStorage\src\FileSystem;
use src\entities\email\Email as EmailNorm;
use common\models\Email;
use modules\fileStorage\src\entity\fileClient\FileClient;
use modules\fileStorage\src\entity\fileLead\FileLead;
use modules\fileStorage\src\entity\fileCase\FileCase;
use modules\fileStorage\src\entity\fileCase\FileCaseRepository;
use modules\fileStorage\src\services\url\UrlGenerator;
use modules\fileStorage\src\entity\fileStorage\FileStorage;
use modules\fileStorage\src\services\CreateByApiDto;
use yii\helpers\ArrayHelper;

/**
 * Class AttachmentsService
 *
 * @property FileSystem $fileSystem
 * @property FileStorageRepository $fileStorageRepository
 * @property FileClientRepository $fileClientRepository
 * @property FileLeadRepository $fileLeadRepository
 * @property FileCaseRepository $fileCaseRepository
 * @property UrlGenerator $urlGenerator
 *
 * @property int|null $leadId
 * @property int|null $caseId
 * @property int|null $clientId
 * @property array $attachments
 * @property Email|EmailNorm $email
 *
 */
class AttachmentsService
{
    public ?int $leadId;
    public ?int $caseId;
    public ?int $clientId;
    public array $attachments = [];

    private $email;
    private FileSystem $fileSystem;
    private FileStorageRepository $fileStorageRepository;
    private FileClientRepository $fileClientRepository;
    private FileLeadRepository $fileLeadRepository;
    private FileCaseRepository $fileCaseRepository;
    private UrlGenerator $urlGenerator;

    /**
     *
     * @param Email|EmailNorm $email
     */
    public function __construct($email)
    {
        $this->email = $email;
        $lead = $email->lead;
        $this->leadId = $lead->id ?? null;
        $case = $email->case;
        $this->caseId = $case->cs_id ?? null;
        $client = $email->client;
        $this->clientId = $client->id ?? null;

        $this->fileStorageRepository = Yii::createObject(FileStorageRepository::class);
        $this->fileClientRepository = Yii::createObject(FileClientRepository::class);
        $this->fileLeadRepository = Yii::createObject(FileLeadRepository::class);
        $this->fileCaseRepository = Yii::createObject(FileCaseRepository::class);
        $this->fileSystem = Yii::createObject(FileSystem::class);
        $this->urlGenerator = Yii::createObject(UrlGenerator::class);
    }

    public function processingFile(string $path): ?array
    {
        if (!$this->fileSystem->fileExists($path)) {
            return null;
        }

        try {
            $fileStorage = $this->fileStorage($path);
        } catch (\Throwable $throwable) {
            $message = AppHelper::throwableLog($throwable);
            $message['email'] = ArrayHelper::toArray($this->email);
            \Yii::error($message, 'AttachmentsService:processingFile:fileStorage');
            return null;
        }

        if ($this->clientId) {
            $this->fileToClient($fileStorage->fs_id);
        }
        if ($this->caseId) {
            $this->fileToCase($fileStorage->fs_id);
        }
        if ($this->leadId) {
            $this->fileToLead($fileStorage->fs_id);
        }

        return [
            'value' => $fileStorage->fs_path,
            'name' => $fileStorage->fs_name,
            'type_id' => $fileStorage->fs_private ? $this->urlGenerator::TYPE_PRIVATE : $this->urlGenerator::TYPE_PUBLIC,
            'uid' => $fileStorage->fs_uid,
        ];
    }

    public function fileStorage(string $path): FileStorage
    {
        $createByApiDto = CreateByApiDto::createWithFile($path, $this->fileSystem);
        $fileStorage = FileStorage::createByEmail($createByApiDto);
        $this->fileStorageRepository->save($fileStorage);

        return $fileStorage;
    }

    public function fileToClient(int $fileStorageId): void
    {
        $this->fileClientRepository->save(FileClient::create($fileStorageId, $this->clientId));
    }

    public function fileToLead(int $fileStorageId): void
    {
        $this->fileLeadRepository->save(FileLead::create($fileStorageId, $this->leadId));
    }

    public function fileToCase(int $fileStorageId): void
    {
        $this->fileCaseRepository->save(FileCase::create($fileStorageId, $this->caseId));
    }
}
