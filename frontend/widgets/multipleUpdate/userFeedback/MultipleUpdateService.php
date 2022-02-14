<?php

namespace frontend\widgets\multipleUpdate\userFeedback;

use modules\user\userFeedback\entity\UserFeedback;
use modules\user\userFeedback\service\UserFeedbackService;
use yii\bootstrap4\Html;

/**
 * Class LeadMultiUpdateService
 *
 * @property array $report
 */
class MultipleUpdateService
{
    private $report;
    private UserFeedbackService $userFeedbackService;

    public function __construct(UserFeedbackService $service)
    {
        $this->userFeedbackService = $service;
        $this->report = [];
    }

    /**
     * @param MultipleUpdateForm $form
     * @return array
     */
    public function update(MultipleUpdateForm $form): array
    {
        foreach ($form->ids as $id) {
            $userFeedback = UserFeedback::findOne($id);
            if (!$userFeedback) {
                $this->addMessage('Not found UserFeedback: ' . $id);
                continue;
            }

            //if (!$lead->isAvailableForMultiUpdate() && !$form->authUserIsAdmin()) {
//            if (!Auth::can('leadSearchMultipleUpdate', ['lead' => $lead]) && !$form->authUserIsAdmin()) {
//                $this->addMessage('Lead ID: ' . $leadId . ' with status "' . $lead->getStatusName() . '" is not available for Multiple Update (permission: leadSearchMultipleUpdate)'); //  Available only status: Processing, FollowUp, Hold, Trash, Snooze
//                continue;
//            }
            $form->typeId ? $typeId = $form->typeId : $typeId = null;
            try {
                $this->userFeedbackService->updateStatusAndTypeId($userFeedback, $form->statusId, $typeId);
            } catch (\RuntimeException $e) {
                $this->addMessage('UserFeedback(' . $id . ') saving failed: ' . $e->getMessage());
            } catch (\Throwable $e) {
                \Yii::error($e, 'userFeedback:MultipleUpdateService:update');
                $this->addMessage('UserFeedback(' . $id . ') saving failed: server error');
            }
        }

        return $this->report;
    }

    /**
     * @param array $reports
     * @return string
     */
    public function formatReport(array $reports): string
    {
        if (!$reports) {
            return '';
        }

        $out = '<ul>';
        foreach ($reports as $report) {
            $out .= Html::tag('li', Html::tag('span', $report, ['style' => 'color:red']));
        }
        return $out . '</ul>';
    }

    /**
     * @param string $message
     */
    private function addMessage(string $message): void
    {
        $this->report[] = $message;
    }
}
