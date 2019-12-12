<?php

namespace frontend\widgets\multipleUpdate\redialAll;

use common\models\Lead;
use common\models\LeadQcall;
use sales\repositories\lead\LeadQcallRepository;
use sales\services\lead\qcall\QCallService;
use yii\db\ActiveQuery;

/**
 * Class UpdateAllService
 *
 * @property QCallService $service
 * @property LeadQcallRepository $repository
 */
class UpdateAllService
{
    private $service;
    private $repository;

    /**
     * @param QCallService $service
     * @param LeadQcallRepository $repository
     */
    public function __construct(QCallService $service, LeadQcallRepository $repository)
    {
        $this->service = $service;
        $this->repository = $repository;
    }

    /**
     * @param UpdateAllForm $form
     * @return array
     */
    public function update(UpdateAllForm $form): array
    {
        $report = [];

        $all = $this->getAll($form);

        if ($form->isRemove()) {
            return $this->remove($all);
        }

        if (!$form->isNotSelectedWeight()) {
            return $this->updateWeight($form->weight, $all);
        }

        return $report;

    }

    /**
     * @param int $weight
     * @param array $query
     * @return array
     */
    private function updateWeight(int $weight, array $query): array
    {
        $report = [];
        foreach ($query as $item) {
            /** @var $item LeadQcall */
            try {
                $item->updateWeight($weight);
                $this->repository->save($item);
                $report[] = 'Record with Lead Id: ' . $item->lqc_lead_id . ' updated weight';
            } catch (\Throwable $e) {
                $report[] = 'Record with Lead Id: ' . $item->lqc_lead_id . ' NOT UPDATED WEIGHT';
            }
        }
        return $report;
    }

    /**
     * @param array $query
     * @return array
     */
    private function remove(array $query): array
    {
        $report = [];
        foreach ($query as $item) {
            /** @var $item LeadQcall */
            try {
                $this->service->remove($item->lqc_lead_id);
                $report[] = 'Record with Lead Id: ' . $item->lqc_lead_id . ' removed';
            } catch (\Throwable $e) {
                $report[] = 'Record with Lead Id: ' . $item->lqc_lead_id . ' NOT REMOVED';
            }
        }
        return $report;
    }

    /**
     * @param UpdateAllForm $form
     * @return array
     */
    private function getAll(UpdateAllForm $form): array
    {
        return LeadQcall::find()->innerJoinWith(['lqcLead' => static function(ActiveQuery $query) use ($form){
            if ($form->statusId) {
                $query->andOnCondition([Lead::tableName() . '.status' => $form->statusId]);
            }
            if ($form->projectId) {
                $query->andOnCondition([Lead::tableName() . '.project_id' => $form->projectId]);
            }
        }])->all();
    }
}
