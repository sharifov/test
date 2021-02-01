<?php

namespace sales\guards\call;

use common\models\Call;
use common\models\Employee;

/**
 * Class CallDisplayGuard
 * @package sales\guards\call
 *
 * @property $canDisplayJoinUserBtn bool
 */
class CallDisplayGuard
{
    private ?bool $canDisplayJoinUserBtn = null;

    public function canDisplayJoinUserBtn(Call $call, Employee $user): bool
    {
        if ($this->canDisplayJoinUserBtn !== null) {
            return $this->canDisplayJoinUserBtn;
        }

        $participant = $call->currentParticipant;
        $callIsTypeAgent = $participant && $participant->isAgent();
        $this->canDisplayJoinUserBtn = $callIsTypeAgent
            && (bool)(\Yii::$app->params['settings']['voip_conference_base'] ?? false)
            && $user->can('/phone/ajax-join-to-conference')
            && ($call->isIn() || $call->isOut() || $call->isReturn())
            && $call->isStatusInProgress();
        return $this->canDisplayJoinUserBtn;
    }
}
