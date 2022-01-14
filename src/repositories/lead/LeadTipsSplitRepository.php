<?php

namespace src\repositories\lead;

use common\models\TipsSplit;

class LeadTipsSplitRepository
{
    public function save(TipsSplit $tips): int
    {
        if (!$tips->save()) {
            throw new \RuntimeException('TipsSplit saving failed: ' . $tips->getErrorSummary(true)[0]);
        }
        return $tips->ts_id;
    }
}
