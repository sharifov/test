<?php

namespace sales\helpers\clientChat;

use common\models\Employee;
use sales\helpers\user\UserFinder;
use sales\model\clientChat\entity\ClientChat;
use Yii;

/**
 * Class ClientChatIframeHelper
 */
class ClientChatIframeHelper
{
    private ClientChat $clientChat;
    private Employee $employee;
    private string $rcUrl;
    private int $randInt;
    private bool $readOnly;

    public const DEFAULT_STYLE = 'border: none; width: 100%; height: 100%;';
    public const DEFAULT_CLASS = '_rc-iframe';
    public const DEFAULT_ONLOAD_FUNCTION = 'removeCcLoadFromIframe();';

    /**
     * ClientChatIframeHelper constructor.
     * @param ClientChat $clientChat
     * @param Employee|null $user
     */
    public function __construct(ClientChat $clientChat, ?Employee $user = null)
    {
        $this->employee = UserFinder::getOrFind($user);
        $this->clientChat = $clientChat;
        $this->rcUrl = Yii::$app->rchat->host  . '/home';
        $this->randInt = $this->setRandInt();
        $this->setReadOnly(null);
    }

    public function generateIframeSrc(): string
    {
        $params['layout'] = 'embedded';
        $params['resumeToken'] = $this->getUserRcAuthToken();
        $params['rand'] = $this->getRandInt();
        if ($this->isReadonly()) {
            $params['readonly'] = 'true';
        }
        $params['goto'] = $this->getLiveGoto();

        return $this->rcUrl . '?' . http_build_query($params);
    }

    public function generateIframeId(): string
    {
        return '_rc-' . $this->clientChat->cch_id;
    }

    public function generateIframeName(): string
    {
        return '_' . $this->getRandInt() . '_' . $this->clientChat->cch_status_id;
    }

    public function generateIframe(string $class = '', string $style = '', string $onload = ''): string
    {
        return '<iframe class="' . self::DEFAULT_CLASS . ' ' . $class . '"
            style="' . self::DEFAULT_STYLE . ' ' . $style . '"
            onload="' . self::DEFAULT_ONLOAD_FUNCTION . $onload . '"
            src="' . $this->generateIframeSrc() . '"
            id="' . $this->generateIframeId() . '"
            name="' . $this->generateIframeName() . '"
            data-rid="' . $this->clientChat->cch_rid . '"
            data-cch-id="' . $this->clientChat->cch_id . '"
            data-is-closed="' . (int) $this->clientChat->isInClosedStatusGroup() . '"></iframe>';
    }

    public function isReadonly(): bool
    {
        return $this->readOnly;
    }

    public function getUserRcAuthToken(): string
    {
        return $this->employee->userProfile ? $this->employee->userProfile->up_rc_auth_token : '';
    }

    private function getLiveGoto(): string
    {
        $readOnly = $this->isReadonly() ? '&readonly=true' : '';
        return urlencode('/live/' . $this->clientChat->cch_rid . '?layout=embedded' . $readOnly);
    }

    private function getRandInt(): int
    {
        return $this->randInt;
    }

    public function setRandInt(): int
    {
        try {
            return $this->randInt = random_int(1, 999);
        } catch (\Throwable $throwable) {
            return $this->randInt = time();
        }
    }

    public function setReadOnly(?bool $readOnly = null): ClientChatIframeHelper
    {
        if ($readOnly === null) {
            $this->readOnly = (
                !$this->clientChat->isOwner($this->employee->getId()) ||
                ($this->clientChat->isInClosedStatusGroup())
            );
            return $this;
        }
        $this->readOnly = $readOnly;
        return $this;
    }
}
