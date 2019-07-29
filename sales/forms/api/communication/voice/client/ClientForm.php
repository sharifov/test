<?php

namespace sales\forms\api\communication\voice\client;

use sales\forms\CompositeForm;

/**
 * Class ClientForm
 * @property $c_id;
 * @property $api_user_id;
 * @property $c_call_status;
 * @property $c_project_id;
 * @property $c_endpoint;
 * @property $action;
 * @property $type;
 *
 * @property CallForm $call
 * @property CallDataForm $callData
 */
class ClientForm extends CompositeForm
{

    public $c_id;
    public $api_user_id;
    public $c_call_status;
    public $c_project_id;
    public $c_endpoint;
    public $action;
    public $type;

    public function __construct($config = [])
    {
        $this->call = new CallForm();
        $this->callData = new CallDataForm();
        parent::__construct($config);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['c_id', 'integer'],
            ['api_user_id', 'integer'],
            ['c_call_status', 'in', 'range' => ['ringing']],
            ['c_project_id', 'integer'],
            ['c_endpoint', 'in', 'range' => ['number']],
            ['action', 'in', 'range' => ['update']],
            ['type', 'in', 'range' => ['voip_client']],
        ];
    }

    /**
     * @return array
     */
    public function internalForms(): array
    {
        return ['call', 'callData'];
    }
}
