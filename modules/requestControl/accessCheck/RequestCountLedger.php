<?php

namespace modules\requestControl\accessCheck;

/**
 * This class determines requests that actually exist.
 *
 * Class RequestCountLedger
 * @package modules\requestControl\accessCheck
 */
class RequestCountLedger
{
    /** @var int indicate the global request count */
    private $global;
    /** @var int indicate the request count to current resource */
    private $local;

    /**
     * RequestCountLedger constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->global = (isset($data['global'])) ? (int)$data['global'] : 0;
        $this->local = (isset($data['local'])) ? (int)$data['local'] : 0;
    }

    /**
     * @return int
     */
    public function getGlobal()
    {
        return $this->global;
    }

    /**
     * @return int
     */
    public function getLocal()
    {
        return $this->local;
    }
}
