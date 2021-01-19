<?php

namespace modules\fileStorage\src\services;

use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToDeleteFile;
use League\Flysystem\UnableToMoveFile;
use modules\fileStorage\src\entity\fileStorage\events\FileEditedEvent;
use modules\fileStorage\src\entity\fileStorage\events\FileRemovedEvent;
use modules\fileStorage\src\entity\fileStorage\events\FileRenamedEvent;
use modules\fileStorage\src\entity\fileStorage\FileStorageQuery;
use modules\fileStorage\src\entity\fileStorage\FileStorageRepository;
use modules\fileStorage\src\FileSystem;
use modules\fileStorage\src\useCase\fileStorage\update\EditForm;
use sales\dispatchers\EventDispatcher;

/**
 * Class FileStorageService
 *
 * @property FileStorageRepository $repository
 * @property FileSystem $fileSystem
 * @property EventDispatcher $eventDispatcher
 */
class FileStorageService
{
    private FileStorageRepository $repository;
    private FileSystem $fileSystem;
    private EventDispatcher $eventDispatcher;

    public function __construct(
        FileStorageRepository $repository,
        FileSystem $fileSystem,
        EventDispatcher $eventDispatcher
    ) {
        $this->repository = $repository;
        $this->fileSystem = $fileSystem;
        $this->eventDispatcher = $eventDispatcher;
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
        $this->eventDispatcher->dispatch(new FileEditedEvent($form->fs_title));
    }

    public function remove(int $id): void
    {
        $file = $this->repository->find($id);
        $relations = FileStorageQuery::getRelations($file->fs_id);

        try {
            $this->fileSystem->delete($file->fs_path);
        } catch (FilesystemException | UnableToDeleteFile $e) {
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => 'Remove file from filesystem error.',
                'error' => $e->getMessage(),
            ], 'FileStorageService:remove');
            throw new \DomainException('Server error. Please try again later.');
        }

        try {
            $this->repository->remove($file);
            $this->eventDispatcher->dispatch(
                new FileRemovedEvent(
                    $file->fs_id,
                    $relations
                )
            );
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => 'Remove file from filesystem error. File was deleted. But Records on DataBase was not deleted.',
                'fileId' => $id,
                'error' => $e->getMessage(),
            ], 'FileStorageService:remove');
            throw new \DomainException('File was deleted. But Records on DataBase was not deleted. Please contact administrator.');
        }
    }

    public function rename(int $id, string $name): void
    {
        $file = $this->repository->find($id);
        $oldName = $file->fs_name;
        $oldLocation = $file->fs_path;

        $file->rename($name);
        $newName = $file->fs_name;
        $newLocation = $file->fs_path;

        try {
            $this->fileSystem->rename($oldLocation, $newLocation);
        } catch (FilesystemException | UnableToMoveFile $e) {
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => 'Rename file on filesystem error.',
                'error' => $e->getMessage(),
            ], 'FileStorageService:rename');
            throw new \DomainException('Server error. Please try again later.');
        }

        try {
            $this->repository->save($file);
            $this->eventDispatcher->dispatch(
                new FileRenamedEvent(
                    $file->fs_id,
                    $oldName,
                    $newName,
                    $oldLocation,
                    $newLocation
                )
            );
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => 'Rename file on filesystem error. File was renamed. But Records on DataBase was not changed.',
                'fileId' => $id,
                'error' => $e->getMessage(),
            ], 'FileStorageService:rename');
            throw new \DomainException('File was renamed. But Records on DataBase was not renamed. Please contact administrator.');
        }
    }
}
