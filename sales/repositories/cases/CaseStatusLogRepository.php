<?php

namespace sales\repositories\cases;

use sales\entities\cases\CaseStatusLog;

/**
 * Class CaseStatusLogRepository
 */
class CaseStatusLogRepository
{

    /**
     * @param int $caseId
     * @return CaseStatusLog|null
     */
    public function getPrevious(int $caseId): ?CaseStatusLog
    {
        if ($log = CaseStatusLog::find()->andWhere(['csl_case_id' => $caseId])->orderBy(['csl_id' => SORT_DESC])->limit(1)->one()) {
            return $log;
        }
        return null;
    }

    /**
     * @param CaseStatusLog $log
     * @return int
     */
    public function save(CaseStatusLog $log): int
    {
        if (!$log->save(false)) {
            throw new \RuntimeException('Saving error');
        }
        return $log->csl_id;
    }

    /**
     * @param CaseStatusLog $log
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function remove(CaseStatusLog $log): void
    {
        if (!$log->delete()) {
            throw new \RuntimeException('Removing error');
        }
    }
}
