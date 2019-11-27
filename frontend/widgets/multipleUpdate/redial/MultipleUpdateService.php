<?php

namespace frontend\widgets\multipleUpdate\redial;

use common\models\LeadFlow;
use common\models\LeadQcall;
use sales\repositories\lead\LeadFlowRepository;
use sales\repositories\lead\LeadQcallRepository;
use sales\services\lead\qcall\QCallService;

/**
 * Class MultipleUpdateService
 *
 * @property  QCallService $service
 * @property  LeadQcallRepository $qCallRepository
 * @property  LeadFlowRepository $leadFlowRepository
 */
class MultipleUpdateService
{
    private $service;
    private $qCallRepository;
    private $leadFlowRepository;

    /**
     * @param QCallService $service
     * @param LeadQcallRepository $qCallRepository
     * @param LeadFlowRepository $leadFlowRepository
     */
    public function __construct(
        QCallService $service,
        LeadQcallRepository $qCallRepository,
        LeadFlowRepository $leadFlowRepository
    )
    {
        $this->service = $service;
        $this->qCallRepository = $qCallRepository;
        $this->leadFlowRepository = $leadFlowRepository;
    }

    /**
     * @param MultipleUpdateForm $form
     * @return string
     */
    public function update(MultipleUpdateForm $form): string
    {
        $report = [];

        if ($form->isRemove()) {
            return $this->formatReport($this->remove($form->ids));
        }

        if ($form->isAttempts()) {
            $report = array_merge($report, $this->changeAttempts($form->attempts, $form->ids));
        }

        if ($form->isAnyFieldForMultipleUpdate()) {
            $report = array_merge($report, $this->multipleUpdate($form));
        }

        return $this->formatReport($report);
    }

    /**
     * @param array $report
     * @return string
     */
    private function formatReport(array $report): string
    {
        $format = '<ul>';
        foreach ($report as $item) {
            $format .= '<li>' . $item . '</li>';
        }
        $format .= '</ul>';
        return $format;
    }

    /**
     * @param MultipleUpdateForm $form
     * @return array
     */
    private function multipleUpdate(MultipleUpdateForm $form): array
    {
        $report = [];
        foreach ($form->ids as $id) {
            if (!$qCall = LeadQcall::findOne($id)) {
                $report[] = 'Record with Lead Id: ' . $id . ' - not found';
                continue;
            }
            try {
                $qCall->multipleUpdate($form->weight, $form->from, $form->to, $form->created);
                $this->qCallRepository->save($qCall);
                $report[] = 'Update record with Lead id: ' . $id . ' - success.';
            } catch (\Throwable $e) {
                $report[] = 'Update record with Lead id: ' . $id . ' - error.';
            }
        }
        return $report;
    }

    /**
     * @param int $attempts
     * @param array $leadIds
     * @return array
     */
    private function changeAttempts(int $attempts, array $leadIds): array
    {
        $report = [];
        foreach ($leadIds as $leadId) {
            if ($leadFlow = LeadFlow::find()->last($leadId)) {
                try {
                    $leadFlow->changeAttempts($attempts);
                    $this->leadFlowRepository->save($leadFlow);
                    $report[] = 'Update attempts Lead id: ' . $leadId . ' - success.';
                } catch (\Throwable $e) {
                    $report[] = 'Update attempts Lead id: ' . $leadId . ' - error.' . $e->getMessage();
                }
            } else {
                $report[] = 'Update attempts Lead id: ' . $leadId . ' - not found lastLeadFlow.';
            }
        }
        return $report;
    }

    /**
     * @param array $leadIds
     * @return array
     */
    private function remove(array $leadIds): array
    {
        $report = [];
        foreach ($leadIds as $leadId) {
            try {
                $this->service->remove($leadId);
                $report[] = 'Delete record with Lead id: ' . $leadId . ' - success.';
            } catch (\Throwable $e) {
                $report[] = 'Delete record with Lead id: ' . $leadId . ' - error.';
            }
        }
        return $report;
    }
}
