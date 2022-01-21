<?php

namespace frontend\widgets\multipleUpdate\userFeedback;

use modules\user\userFeedback\entity\UserFeedback;
use modules\user\userFeedback\UserFeedbackRepository;
use yii\bootstrap4\Html;

/**
 * Class LeadMultiUpdateService
 *
 * @property array $report
 */
class MultipleUpdateService
{
    private $report;
    private UserFeedbackRepository $userFeedbackRepository;

    public function __construct(UserFeedbackRepository $repository)
    {
        $this->userFeedbackRepository = $repository;
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

            $userFeedback->uf_status_id = $form->statusId;
            if ($form->typeId) {
                $userFeedback->uf_type_id = $form->typeId;
            }

            try {
                $this->userFeedbackRepository->save($userFeedback);
            } catch (\RuntimeException $e) {
                $this->addMessage('UserFeedback(' . $id . ') saving failed: ' . $e->getMessage());
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
            $out .= Html::tag('li', Html::tag('span', $report, ['style' => 'color: #28a048']));
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
