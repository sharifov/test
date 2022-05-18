<?php

namespace frontend\widgets\multipleUpdate\cases;

use modules\cases\src\abac\CasesAbacObject;
use modules\cases\src\abac\dto\CasesAbacDto;
use src\entities\cases\Cases;
use src\entities\cases\CasesStatus;
use src\services\cases\CasesManageService;
use Yii;

/**
 * Class MultipleUpdateService
 *
 * @property CasesManageService $service
 * @property array $messages
 */
class MultipleUpdateService
{
    private $service;
    private $messages = [];

    public function __construct(CasesManageService $service)
    {
        $this->service = $service;
    }

    public function update(MultipleUpdateForm $form): array
    {
        foreach ($form->ids as $id) {
            if (!$case = Cases::findOne($id)) {
                $this->addErrorMessage('ID ' . $id . ' not found');
                continue;
            }

            $caseAbacDto = new CasesAbacDto($case, $form->statusId);
            $caseAbacDto->pqc_status = $case->productQuoteChange->pqc_status_id ?? null;
            $caseAbacDto->pqr_status = $case->productQuoteRefund->pqr_status_id ?? null;

            if ($form->isChangeStatus()) {
                /** @abac new $caseAbacDto, CasesAbacObject::OBJ_CASE_STATUS_ROUTE_RULES, CasesAbacObject::ACTION_TRANSFER, Case Status transfer rules on multi update */
                if (!Yii::$app->abac->can($caseAbacDto, CasesAbacObject::OBJ_CASE_STATUS_ROUTE_RULES, CasesAbacObject::ACTION_TRANSFER)) {
                    continue;
                }
                $this->changeStatus($form, $case);
                continue;
            }
            if ($form->isProcessing()) {
                /** @abac new $caseAbacDto, CasesAbacObject::OBJ_CASE_STATUS_ROUTE_RULES, CasesAbacObject::ACTION_TRANSFER, Case Status transfer rules on multi update */
                if (!Yii::$app->abac->can($caseAbacDto, CasesAbacObject::OBJ_CASE_STATUS_ROUTE_RULES, CasesAbacObject::ACTION_TRANSFER)) {
                    continue;
                }
                $this->processing($form, $case);
            }
        }
        return $this->messages;
    }

    private function processing(MultipleUpdateForm $form, Cases $case): void
    {
        try {
            $this->service->multipleChangeStatusProcessing($case->cs_id, $form->userId, $form->getCreatorId());
            $this->addSuccessMessage($this->movedProcessingMessage($case));
        } catch (\DomainException $e) {
            $this->addErrorMessage('ID ' . $case->cs_id . ' ' . $e->getMessage());
        }
    }

    private function changeStatus(MultipleUpdateForm $form, Cases $case): void
    {
        try {
            if ($form->isPending()) {
                $this->service->pending($case, $form->getCreatorId(), 'Multiple Update');
                $this->addSuccessMessage($this->movedStatusMessage($case));
                return;
            }
            if ($form->isFollowUp()) {
                $this->service->followUp($case, $form->getCreatorId(), $form->message, $form->getConvertedDeadline());
                $this->addSuccessMessage($this->movedStatusMessage($case));
                return;
            }
            if ($form->isSolved()) {
                $this->service->solved($case, $form->getCreatorId(), 'Multiple Update');
                $this->addSuccessMessage($this->movedStatusMessage($case));
                return;
            }
            if ($form->isTrash()) {
                $this->service->trash($case, $form->getCreatorId(), $form->message);
                $this->addSuccessMessage($this->movedStatusMessage($case));
                return;
            }
            if ($form->isAwaiting()) {
                $this->service->awaiting($case, $form->getCreatorId(), 'Multiple Update');
                $this->addSuccessMessage($this->movedStatusMessage($case));
            }
            if ($form->isAutoProcessing()) {
                $this->service->autoProcessing($case, $form->getCreatorId(), 'Multiple Update');
                $this->addSuccessMessage($this->movedStatusMessage($case));
            }
            if ($form->isError()) {
                $this->service->error($case, $form->getCreatorId(), 'Multiple Update');
                $this->addSuccessMessage($this->movedStatusMessage($case));
            }
            if ($form->isNew()) {
                $this->service->new($case, $form->getCreatorId(), 'Multiple Update');
                $this->addSuccessMessage($this->movedStatusMessage($case));
            }
        } catch (\DomainException $e) {
            $this->addErrorMessage('ID ' . $case->cs_id . ' ' . $e->getMessage());
        }
    }

    private function movedStatusMessage(Cases $case): string
    {
        return 'ID ' . $case->cs_id .  ' moved to ' . CasesStatus::getName($case->cs_status);
    }

    private function movedProcessingMessage(Cases $case): string
    {
        return 'ID ' . $case->cs_id .  ' moved to ' . CasesStatus::getName($case->cs_status) . ' with owner: ' . $case->owner->username;
    }

    public function formatMessages(Message ...$messages): string
    {
        if (!$messages) {
            return '';
        }

        $out = '<ul>';
        foreach ($messages as $message) {
            $out .= '<li>' . $message->format() . '</li>';
        }
        return $out . '</ul>';
    }

    private function addMessage(Message $message): void
    {
        $this->messages[] = $message;
    }

    private function addSuccessMessage(string $message): void
    {
        $this->addMessage(new SuccessMessage($message));
    }

    private function addErrorMessage(string $message): void
    {
        $this->addMessage(new ErrorMessage($message));
    }
}
