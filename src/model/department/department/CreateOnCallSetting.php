<?php

namespace src\model\department\department;

class CreateOnCallSetting
{
    public bool $createOnGeneralLineCall;
    public bool $createOnDirectCall;
    public bool $createOnRedirectCall;

    public function __construct(array $params)
    {
        $this->createOnGeneralLineCall = (bool)($params['createOnGeneralLineCall'] ?? false);
        $this->createOnDirectCall = (bool)($params['createOnDirectCall'] ?? false);
        $this->createOnRedirectCall = (bool)($params['createOnRedirectCall'] ?? false);
    }
}
