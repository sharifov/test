<?php

namespace sales\model\department\department;

/**
 * Class WarmTransferSettings
 *
 * @property int|null $timeout
 * @property bool|null $autoUnholdEnabled
 */
class WarmTransferSettings
{
    public ?int $timeout;
    public ?bool $autoUnholdEnabled;

    public function __construct(array $params)
    {
        if (!array_key_exists('timeout', $params)) {
            $this->timeout = null;
        } else {
            $this->timeout = (int)$params['timeout'];
        }
        if (!array_key_exists('auto_unhold_enabled', $params)) {
            $this->autoUnholdEnabled = null;
        } else {
            $this->autoUnholdEnabled = (bool)$params['auto_unhold_enabled'];
        }
    }
}
