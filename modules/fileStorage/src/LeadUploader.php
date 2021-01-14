<?php

namespace modules\fileStorage\src;

use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToWriteFile;
use modules\fileStorage\src\entity\fileClient\FileClient;
use modules\fileStorage\src\entity\fileClient\FileClientRepository;
use modules\fileStorage\src\entity\fileLead\FileLead;
use modules\fileStorage\src\entity\fileLead\FileLeadRepository;
use modules\fileStorage\src\entity\fileStorage\events\FileCreatedByLeadEvent;
use modules\fileStorage\src\entity\fileStorage\FileStorage;
use modules\fileStorage\src\entity\fileStorage\FileStorageRepository;
use modules\fileStorage\src\entity\fileStorage\Path;
use modules\fileStorage\src\entity\fileStorage\Uid;
use sales\dispatchers\EventDispatcher;
use sales\services\PostgresTransactionManager;
use yii\web\UploadedFile;

/**
 * Class LeadUploader
 *
 * @property FileSystem $fileSystem
 * @property FileStorageRepository $fileStorageRepository
 * @property FileClientRepository $fileClientRepository
 * @property FileLeadRepository $fileLeadRepository
 * @property PostgresTransactionManager $postgresTransactionManager
 * @property EventDispatcher $eventDispatcher
 */
class LeadUploader
{
    private FileSystem $fileSystem;
    private FileStorageRepository $fileStorageRepository;
    private FileClientRepository $fileClientRepository;
    private FileLeadRepository $fileLeadRepository;
    private PostgresTransactionManager $postgresTransactionManager;
    private EventDispatcher $eventDispatcher;

    public function __construct(
        FileSystem $fileStorage,
        FileStorageRepository $fileStorageRepository,
        FileClientRepository $fileClientRepository,
        FileLeadRepository $fileLeadRepository,
        PostgresTransactionManager $postgresTransactionManager,
        EventDispatcher $eventDispatcher
    ) {
        $this->fileSystem = $fileStorage;
        $this->fileStorageRepository = $fileStorageRepository;
        $this->fileClientRepository = $fileClientRepository;
        $this->fileLeadRepository = $fileLeadRepository;
        $this->postgresTransactionManager = $postgresTransactionManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function upload(int $leadId, int $clientId, string $projectKey, UploadedFile $file): void
    {
        /** @var $fileStorage FileStorage */
        /** @var $fileClient FileClient */
        /** @var $fileLead FileLead */
        [$fileStorage, $fileClient, $fileLead] = $this->saveFile($leadId, $clientId, $projectKey, $file);

        try {
            $stream = fopen($file->tempName, 'r+');
            $this->fileSystem->writeStream($fileStorage->fs_path, $stream);
            fclose($stream);
            $this->eventDispatcher->dispatch(new FileCreatedByLeadEvent(
                $leadId,
                $fileStorage->fs_name,
                $fileStorage->fs_title,
                $fileStorage->fs_path
            ));
        } catch (FilesystemException | UnableToWriteFile $e) {
            if (isset($stream) && $stream !== false && is_resource($stream)) {
                fclose($stream);
            }
            $this->error('Upload FileStorage by Lead error.', $e->getMessage(), $leadId, $clientId, $projectKey);
            $this->removeFile($fileStorage, $fileClient, $fileLead, $projectKey);
            throw new \DomainException('Server error. Please try again.');
        } catch (\Throwable $e) {
            if (isset($stream) && $stream !== false && is_resource($stream)) {
                fclose($stream);
            }
            $this->error('Upload FileStorage by Lead error.', $e->getMessage(), $leadId, $clientId, $projectKey);
            $this->removeFile($fileStorage, $fileClient, $fileLead, $projectKey);
            throw new \DomainException('Server error. Please try again.');
        }
    }

    private function saveFile(int $leadId, int $clientId, string $projectKey, UploadedFile $file): array
    {
        return $this->postgresTransactionManager->wrap(function () use ($leadId, $clientId, $projectKey, $file) {
            $uid = Uid::next();
            $path = new Path(PathGenerator::byClient($clientId, $projectKey, $file->name, $uid));
            $fileStorage = FileStorage::createByLead(
                $file->name,
                $path,
                $file->size,
                $uid,
                $file->type,
                md5_file($file->tempName),
                new \DateTimeImmutable()
            );
            try {
                $this->fileStorageRepository->save($fileStorage);
            } catch (\yii\db\IntegrityException $e) {
                $error = $e->errorInfo[2];
                if (stripos($error, 'ERROR:  duplicate key value violates unique constraint') === 0) {
                    $this->error('Generated FileStorage duplicate UID', $e->getMessage(), $leadId, $clientId, $projectKey);
                    throw new \DomainException('Server error. Try again.');
                }
            }

            $fileClient = FileClient::create($fileStorage->fs_id, $clientId);
            $this->fileClientRepository->save($fileClient);

            $fileLead = FileLead::create($fileStorage->fs_id, $leadId);
            $this->fileLeadRepository->save($fileLead);

            return [$fileStorage, $fileClient, $fileLead];
        });
    }

    private function removeFile(FileStorage $fileStorage, FileClient $fileClient, FileLead $fileLead, string $projectKey): void
    {
        try {
            $this->postgresTransactionManager->wrap(function () use ($fileStorage, $fileClient, $fileLead) {
                $this->fileLeadRepository->remove($fileLead);
                $this->fileClientRepository->remove($fileClient);
                $this->fileStorageRepository->remove($fileStorage);
            });
        } catch (\Throwable $e) {
            $this->error(
                'File was not uploaded. But Records on DataBase was created and not deleted. FileStorageId: ' . $fileStorage->fs_id,
                $e->getMessage(),
                $fileLead->fld_lead_id,
                $fileClient->fcl_client_id,
                $projectKey
            );
            throw new \DomainException('File was not uploaded. But Records on DataBase was created and not deleted. Please contact administrator.');
        }
    }

    private function error($message, $error, $leadId, $clientId, $projectKey): void
    {
        \Yii::error([
            'message' => $message,
            'error' => $error,
            'leadId' => $leadId,
            'clientId' => $clientId,
            'projectKey' => $projectKey
        ], 'LeadUploader');
    }
}
