<?php

namespace modules\fileStorage\src;

use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToWriteFile;
use modules\fileStorage\src\entity\fileClient\FileClient;
use modules\fileStorage\src\entity\fileClient\FileClientRepository;
use modules\fileStorage\src\entity\fileLead\FileLead;
use modules\fileStorage\src\entity\fileLead\FileLeadRepository;
use modules\fileStorage\src\entity\fileStorage\FileStorage;
use modules\fileStorage\src\entity\fileStorage\FileStorageRepository;
use modules\fileStorage\src\entity\fileStorage\Path;
use modules\fileStorage\src\entity\fileStorage\Uid;
use modules\fileStorage\src\entity\fileUser\FileUser;
use modules\fileStorage\src\entity\fileUser\FileUserRepository;
use modules\fileStorage\src\services\PathGenerator;
use sales\auth\Auth;
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
 * @property FileuserRepository $fileUserRepository
 */
class LeadUploader
{
    private FileSystem $fileSystem;
    private FileStorageRepository $fileStorageRepository;
    private FileClientRepository $fileClientRepository;
    private FileLeadRepository $fileLeadRepository;
    private PostgresTransactionManager $postgresTransactionManager;
    private FileUserRepository $fileUserRepository;

    public function __construct(
        FileSystem $fileStorage,
        FileStorageRepository $fileStorageRepository,
        FileClientRepository $fileClientRepository,
        FileLeadRepository $fileLeadRepository,
        PostgresTransactionManager $postgresTransactionManager,
        FileUserRepository $fileUserRepository
    ) {
        $this->fileSystem = $fileStorage;
        $this->fileStorageRepository = $fileStorageRepository;
        $this->fileClientRepository = $fileClientRepository;
        $this->fileLeadRepository = $fileLeadRepository;
        $this->postgresTransactionManager = $postgresTransactionManager;
        $this->fileUserRepository = $fileUserRepository;
    }

    public function upload(int $leadId, int $clientId, string $projectKey, ?string $title, UploadedFile $file): void
    {
        /** @var $fileStorage FileStorage */
        /** @var $fileClient FileClient */
        /** @var $fileLead FileLead */
        [$fileStorage, $fileClient, $fileLead] = $this->create($leadId, $clientId, $projectKey, $title, $file);

        try {
            $stream = fopen($file->tempName, 'r+');
            $this->fileSystem->writeStream($fileStorage->fs_path, $stream);
            fclose($stream);
            $fileStorage->uploadedByLead($leadId);
            $this->fileStorageRepository->save($fileStorage);
        } catch (FilesystemException | UnableToWriteFile $e) {
            if (isset($stream) && $stream !== false && is_resource($stream)) {
                fclose($stream);
            }
            $this->error('Upload FileStorage by Lead error.', $e->getMessage(), $leadId, $clientId, $projectKey);
            $this->failed($fileStorage, $fileClient->fcl_client_id, $fileLead->fld_lead_id, $projectKey);
            throw new \DomainException('Server error. Please try again.');
        } catch (\Throwable $e) {
            if (isset($stream) && $stream !== false && is_resource($stream)) {
                fclose($stream);
            }
            $this->error('Upload FileStorage by Lead error.', $e->getMessage(), $leadId, $clientId, $projectKey);
            $this->failed($fileStorage, $fileClient->fcl_client_id, $fileLead->fld_lead_id, $projectKey);
            throw new \DomainException('Server error. Please try again.');
        }
    }

    private function create(int $leadId, int $clientId, string $projectKey, ?string $title, UploadedFile $file): array
    {
        return $this->postgresTransactionManager->wrap(function () use ($leadId, $clientId, $projectKey, $title, $file) {
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
                    $this->error('Generated FileStorage duplicate UID', $e->getMessage(), $leadId, $clientId, $projectKey);
                    throw new \DomainException('Server error. Try again.');
                }
                throw $e;
            }

            $fileClient = FileClient::create($fileStorage->fs_id, $clientId);
            $this->fileClientRepository->save($fileClient);

            $fileLead = FileLead::create($fileStorage->fs_id, $leadId);
            $this->fileLeadRepository->save($fileLead);

            $fileUser = FileUser::create($fileStorage->fs_id, Auth::id());
            $this->fileUserRepository->save($fileUser);

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

    private function failed(FileStorage $fileStorage, int $clientId, int $leadId, string $projectKey): void
    {
        try {
            $fileStorage->failed();
            $this->fileStorageRepository->save($fileStorage);
        } catch (\Throwable $e) {
            $this->error(
                'File was not uploaded. Records on DataBase was not marked "Failed". FileStorageId: ' . $fileStorage->fs_id,
                $e->getMessage(),
                $leadId,
                $clientId,
                $projectKey
            );
            throw new \DomainException('File was not uploaded. Records on DataBase was not marked "Failed". Please contact administrator.');
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
