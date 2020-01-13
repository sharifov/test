<?php

namespace frontend\widgets\multipleUpdate\cases;

use sales\entities\cases\Cases;
use sales\entities\cases\CasesStatus;
use sales\services\cases\CasesManageService;
use yii\bootstrap4\Html;

/**
 * Class MultipleUpdateService
 *
 * @property CasesManageService $service
 * @property array $report
 */
class MultipleUpdateService
{
    private $service;
    private $report = [];

    public function __construct(CasesManageService $service)
    {
        $this->service = $service;
    }

    public function update(MultipleUpdateForm $form, int $creatorId): array
    {
        foreach ($form->ids as $id) {
            if (!$case = Cases::findOne($id)) {
                $this->addMessage('Case: ' . $id . ' not found');
                continue;
            }
            if ($form->isChangeStatus()) {
                $this->changeStatus($form, $case, $creatorId);
                continue;
            }
            if ($form->isProcessing()) {
                $this->processing($form, $case, $creatorId);
                continue;
            }
        }
        return $this->report;
    }

    private function processing(MultipleUpdateForm $form, Cases $case, int $creatorId): void
    {
        try {
            $this->service->processing($case, $form->userId, $creatorId);
            $this->addMessage($this->movedProcessingMessage($case));
        } catch (\DomainException $e) {
            $this->addMessage('Case: ' . $case->cs_id . ' ' . $e->getMessage());
        }
    }

    private function changeStatus(MultipleUpdateForm $form, Cases $case, int $creatorId): void
    {
        try {
            if ($form->isPending()) {
                $this->service->pending($case, $creatorId, $form->message);
                $this->addMessage($this->movedStatusMessage($case));
                return;
            }
            if ($form->isFollowUp()) {
                $this->service->followUp($case, $creatorId, $form->message);
                $this->addMessage($this->movedStatusMessage($case));
                return;
            }
            if ($form->isSolved()) {
                $this->service->solved($case, $creatorId, $form->message);
                $this->addMessage($this->movedStatusMessage($case));
                return;
            }
            if ($form->isTrash()) {
                $this->service->trash($case, $creatorId, $form->message);
                $this->addMessage($this->movedStatusMessage($case));
                return;
            }
        } catch (\DomainException $e) {
            $this->addMessage('Case: ' . $case->cs_id . ' ' . $e->getMessage());
        }
    }

    private function movedStatusMessage(Cases $case): string
    {
        return 'Case: ' . $case->cs_id .  ' moved to ' . CasesStatus::getName($case->cs_status);
    }

    private function movedProcessingMessage(Cases $case): string
    {
        return 'Case: ' . $case->cs_id .  ' moved to ' . CasesStatus::getName($case->cs_status) . ' with owner: ' . $case->owner->username;
    }

    public function formatReport(array $reports): string
    {
        if (!$reports) {
            return '';
        }

        $out = '<div class="card" style="margin-bottom: 10px" ><div class="card-body"><ul>';
        foreach ($reports as $report) {
            $out .= Html::tag('li', Html::tag('span', $report, ['style' => 'color: #28a048']));
        }
        return $out . '</ul></div></div>';
    }

    private function addMessage(string $message): void
    {
        $this->report[] = $message;
    }
}
