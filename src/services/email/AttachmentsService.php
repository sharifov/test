<?php

namespace src\services\email;

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

/**
 * Class AttachmentsService
 *
 * @property FileSystem $fileSystem
 * @property FileStorageRepository $fileStorageRepository
 * @property FileClientRepository $fileClientRepository
 * @property FileLeadRepository $fileLeadRepository
 * @property FileCaseRepository $fileCaseRepository
 */
class AttachmentsService
{
    public $leadId;
    public $caseId;
    public $clientId;
    public $attachments = [];

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
        $lead = $email->lead ?? $email->eLead;
        $this->leadId = $lead->id ?? null;
        $case = $email->case ?? $email->eCase;
        $this->caseId = $case->cs_id ?? null;
        $client = $email->client ?? $email->eClient;
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

        $fileStorageId = $this->fileStorage($path);

        if ($this->clientId) {
            $this->fileToClient($fileStorageId);
        }
        if ($this->caseId) {
            $this->fileToCase($fileStorageId);
        }
        if ($this->leadId) {
            $this->fileToLead($fileStorageId);
        }

        return [
            'value' => $fileStorage->fs_path,
            'name' => $fileStorage->fs_name,
            'type_id' => $fileStorage->fs_private ? $this->urlGenerator::TYPE_PRIVATE : $this->urlGenerator::TYPE_PUBLIC,
        ];
    }

    public function fileStorage($path)
    {
        $createByApiDto = new CreateByApiDto($path, $this->fileSystem);
        $fileStorage = FileStorage::createByEmail($createByApiDto);
        $this->fileStorageRepository->save($fileStorage);

        return $fileStorage->fs_id;
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
