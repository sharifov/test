<?php

namespace modules\fileStorage\src;

use modules\fileStorage\src\entity\fileStorage\FileStorageRepository;
use modules\fileStorage\src\useCase\fileStorage\update\EditForm;

/**
 * Class FileStorageService
 *
 * @property FileStorageRepository $repository
 */
class FileStorageService
{
    private FileStorageRepository $repository;

    public function __construct(FileStorageRepository $repository)
    {
        $this->repository = $repository;
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
}
