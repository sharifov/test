<?php

namespace modules\fileStorage\src;

use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToWriteFile;
use modules\fileStorage\src\entity\fileCase\FileCase;
use modules\fileStorage\src\entity\fileCase\FileCaseRepository;
use modules\fileStorage\src\entity\fileClient\FileClient;
use modules\fileStorage\src\entity\fileClient\FileClientRepository;
use modules\fileStorage\src\entity\fileStorage\events\FileCreatedByCaseEvent;
use modules\fileStorage\src\entity\fileStorage\FileStorage;
use modules\fileStorage\src\entity\fileStorage\FileStorageRepository;
use modules\fileStorage\src\entity\fileStorage\Path;
use modules\fileStorage\src\entity\fileStorage\Uid;
use modules\fileStorage\src\services\PathGenerator;
use sales\dispatchers\EventDispatcher;
use sales\services\PostgresTransactionManager;
use yii\web\UploadedFile;

/**
 * Class CaseUploader
 *
 * @property FileSystem $fileSystem
 * @property FileStorageRepository $fileStorageRepository
 * @property FileClientRepository $fileClientRepository
 * @property FileCaseRepository $fileCaseRepository
 * @property PostgresTransactionManager $postgresTransactionManager
 * @property EventDispatcher $eventDispatcher
 */
class CaseUploader
{
    private FileSystem $fileSystem;
    private FileStorageRepository $fileStorageRepository;
    private FileClientRepository $fileClientRepository;
    private FileCaseRepository $fileCaseRepository;
    private PostgresTransactionManager $postgresTransactionManager;
    private EventDispatcher $eventDispatcher;

    public function __construct(
        FileSystem $fileStorage,
        FileStorageRepository $fileStorageRepository,
        FileClientRepository $fileClientRepository,
        FileCaseRepository $fileCaseRepository,
        PostgresTransactionManager $postgresTransactionManager,
        EventDispatcher $eventDispatcher
    ) {
        $this->fileSystem = $fileStorage;
        $this->fileStorageRepository = $fileStorageRepository;
        $this->fileClientRepository = $fileClientRepository;
        $this->fileCaseRepository = $fileCaseRepository;
        $this->postgresTransactionManager = $postgresTransactionManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function upload(int $caseId, int $clientId, string $projectKey, ?string $title, UploadedFile $file): void
    {
        /** @var $fileStorage FileStorage */
        /** @var $fileClient FileClient */
        /** @var $fileCase FileCase */
        [$fileStorage, $fileClient, $fileCase] = $this->create($caseId, $clientId, $projectKey, $title, $file);

        try {
            $stream = fopen($file->tempName, 'r+');
            $this->fileSystem->writeStream($fileStorage->fs_path, $stream);
            fclose($stream);
            $fileStorage->uploaded();
            $this->fileStorageRepository->save($fileStorage);
            $this->eventDispatcher->dispatch(
                new FileCreatedByCaseEvent(
                    $caseId,
                    $fileStorage->fs_name,
                    $fileStorage->fs_title,
                    $fileStorage->fs_path
                )
            );
        } catch (FilesystemException | UnableToWriteFile $e) {
            if (isset($stream) && $stream !== false && is_resource($stream)) {
                fclose($stream);
            }
            $this->error('Upload FileStorage by Case error.', $e->getMessage(), $caseId, $clientId, $projectKey);
            $this->failed($fileStorage, $fileClient->fcl_client_id, $fileCase->fc_case_id, $projectKey);
            throw new \DomainException('Server error. Please try again.');
        } catch (\Throwable $e) {
            if (isset($stream) && $stream !== false && is_resource($stream)) {
                fclose($stream);
            }
            $this->error('Upload FileStorage by Case error.', $e->getMessage(), $caseId, $clientId, $projectKey);
            $this->failed($fileStorage, $fileClient->fcl_client_id, $fileCase->fc_case_id, $projectKey);
            throw new \DomainException('Server error. Please try again.');
        }
    }

    private function create(int $caseId, int $clientId, string $projectKey, ?string $title, UploadedFile $file): array
    {
        return $this->postgresTransactionManager->wrap(function () use ($caseId, $clientId, $projectKey, $title, $file) {
            $uid = Uid::next();
            $path = new Path(PathGenerator::byClient($clientId, $projectKey, $file->name, $uid));
            $fileStorage = FileStorage::createByUpload(
                $file->name,
                $title,
                $path,
                $file->size,
                $uid,
                $file->type,
                md5_file($file->tempName),
                false,
                new \DateTimeImmutable()
            );
            try {
                $this->fileStorageRepository->save($fileStorage);
            } catch (\yii\db\IntegrityException $e) {
                $error = $e->errorInfo[2];
                if (stripos($error, 'ERROR:  duplicate key value violates unique constraint') === 0) {
                    $this->error('Generated FileStorage duplicate UID', $e->getMessage(), $caseId, $clientId, $projectKey);
                    throw new \DomainException('Server error. Try again.');
                }
                throw $e;
            }

            $fileClient = FileClient::create($fileStorage->fs_id, $clientId);
            $this->fileClientRepository->save($fileClient);

            $fileCase = FileCase::create($fileStorage->fs_id, $caseId);
            $this->fileCaseRepository->save($fileCase);

            return [$fileStorage, $fileClient, $fileCase];
        });
    }

    private function removeFile(FileStorage $fileStorage, FileClient $fileClient, FileCase $fileCase, string $projectKey): void
    {
        try {
            $this->postgresTransactionManager->wrap(function () use ($fileStorage, $fileClient, $fileCase) {
                $this->fileCaseRepository->remove($fileCase);
                $this->fileClientRepository->remove($fileClient);
                $this->fileStorageRepository->remove($fileStorage);
            });
        } catch (\Throwable $e) {
            $this->error(
                'File was not uploaded. But Records on DataBase was created and not deleted. FileStorageId: ' . $fileStorage->fs_id,
                $e->getMessage(),
                $fileCase->fc_case_id,
                $fileClient->fcl_client_id,
                $projectKey
            );
            throw new \DomainException('File was not uploaded. But Records on DataBase was created and not deleted. Please contact administrator.');
        }
    }

    private function failed(FileStorage $fileStorage, int $clientId, int $caseId, string $projectKey): void
    {
        try {
            $fileStorage->failed();
            $this->fileStorageRepository->save($fileStorage);
        } catch (\Throwable $e) {
            $this->error(
                'File was not uploaded. Records on DataBase was not marked "Failed". FileStorageId: ' . $fileStorage->fs_id,
                $e->getMessage(),
                $caseId,
                $clientId,
                $projectKey
            );
            throw new \DomainException('File was not uploaded. Records on DataBase was not marked "Failed". Please contact administrator.');
        }
    }

    private function error($message, $error, $caseId, $clientId, $projectKey): void
    {
        \Yii::error([
            'message' => $message,
            'error' => $error,
            'caseId' => $caseId,
            'clientId' => $clientId,
            'projectKey' => $projectKey
        ], 'CaseUploader');
    }
}
