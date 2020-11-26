<?php

namespace frontend\widgets\client;

use sales\model\client\query\ClientLeadCaseCounter;
use yii\base\Widget;

/**
 * Class ClientCounterWidget
 *
 * @property int $clientId
 * @property int $userId
 */
class ClientCounterWidget extends Widget
{
    public $clientId;
    public $userId;

    public function run(): ?string
    {
        if (!$this->clientId) {
            return null;
        }

        $counter = new ClientLeadCaseCounter($this->clientId, $this->userId);

        return $this->render('client_counter', [
            'allLeads' => $counter->countAllLeads(),
            'activeLeads' => $counter->countActiveLeads(),
            'allCases' => $counter->countAllCases(),
            'activeCases' => $counter->countActiveCases()
        ]);
    }
}
