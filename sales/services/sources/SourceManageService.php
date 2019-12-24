<?php

namespace sales\services\sources;

use common\models\Sources;
use sales\repositories\source\SourceRepository;
use sales\services\TransactionManager;

/**
 * Class SourceManageService
 *
 * @property SourceRepository $sourceRepository
 * @property TransactionManager $transactionManager
 */
class SourceManageService
{
    private $sourceRepository;
    private $transactionManager;

    /**
     * @param SourceRepository $sourceRepository
     * @param TransactionManager $transactionManager
     */
    public function __construct(SourceRepository $sourceRepository, TransactionManager $transactionManager)
    {
        $this->sourceRepository = $sourceRepository;
        $this->transactionManager = $transactionManager;
    }

    /**
     * @param int $sourceId
     * @throws \Throwable
     */
    public function setDefault(int $sourceId): void
    {
        $source = $this->sourceRepository->find($sourceId);
        $this->transactionManager->wrap(function () use ($source) {

            Sources::updateAll(['default' => 0], ['project_id' => $source->project_id]);
            $source->default();
            $this->sourceRepository->save($source);

        });
    }
}
