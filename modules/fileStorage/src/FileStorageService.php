<?php

namespace modules\fileStorage\src;

use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToDeleteFile;
use modules\fileStorage\src\entity\fileStorage\FileStorageRepository;
use modules\fileStorage\src\useCase\fileStorage\update\EditForm;

/**
 * Class FileStorageService
 *
 * @property FileStorageRepository $repository
 * @property FileSystem $fileSystem
 */
class FileStorageService
{
    private FileStorageRepository $repository;
    private FileSystem $fileSystem;

    public function __construct(FileStorageRepository $repository, FileSystem $fileSystem)
    {
        $this->repository = $repository;
        $this->fileSystem = $fileSystem;
    }

    public function edit(EditForm $form): void
    {
        $file = $this->repository->find($form->fs_id);
        $file->edit(
            $form->fs_title,
            $form->fs_private,
            new \DateTimeImmutable($form->fs_expired_dt)
        );
        $this->repository->save($file);
    }

    public function remove(int $id): void
    {
        $file = $this->repository->find($id);

        try {
            $this->fileSystem->delete($file->fs_path);
        } catch (FilesystemException | UnableToDeleteFile $e) {
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => 'Remove file from filesystem error.',
                'error' => $e->getMessage(),
            ], 'FileStorageService"remove');
            throw new \DomainException('Server error. Please try again later.');
        }

        try {
            $this->repository->remove($file);
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => 'Remove file from filesystem error. File was deleted. But Records on DataBase was not deleted.',
                'fileId' => $id,
                'error' => $e->getMessage(),
            ], 'FileStorageService"remove');
            throw new \DomainException('File was deleted. But Records on DataBase was not deleted. Please contact administrator.');
        }
    }
}
