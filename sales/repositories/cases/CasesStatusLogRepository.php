<?php

namespace sales\repositories\cases;

use sales\entities\cases\CasesStatusLog;

/**
 * Class CasesStatusLogRepository
 */
class CasesStatusLogRepository
{

    /**
     * @param int $caseId
     * @return CasesStatusLog|null
     */
    public function getPrevious(int $caseId): ?CasesStatusLog
    {
        if ($log = CasesStatusLog::find()->andWhere(['csl_case_id' => $caseId])->orderBy(['csl_id' => SORT_DESC])->limit(1)->one()) {
            return $log;
        }
        return null;
    }

    /**
     * @param CasesStatusLog $log
     * @return int
     */
    public function save(CasesStatusLog $log): int
    {
        if (!$log->save(false)) {
            throw new \RuntimeException('Saving error');
        }
        return $log->csl_id;
    }

    /**
     * @param CasesStatusLog $log
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function remove(CasesStatusLog $log): void
    {
        if (!$log->delete()) {
            throw new \RuntimeException('Removing error');
        }
    }
}
