<?php

namespace sales\forms\api\communication\voice\record;

use sales\forms\CompositeForm;

/**
 * Class RecordForm
 * @property $type
 * @property $action
 * @property $c_id
 * @property $api_user_id
 * @property $c_call_status
 * @property $c_project_id
 * @property $c_endpoint
 *
 * @property CallForm $call
 * @property CallDataForm $callData
 */
class RecordForm extends CompositeForm
{

    public $type;
    public $action;
    public $c_id;
    public $api_user_id;
    public $c_call_status;
    public $c_project_id;
    public $c_endpoint;

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
            ['type', 'in', 'range' => ['voip_record']],
            ['action', 'in', 'range' => ['update']],
            ['c_id', 'integer'],
            ['api_user_id', 'integer'],
            ['c_call_status', 'in', 'range' => ['completed', 'ringing', 'in-progress']],
            ['c_project_id', 'integer'],
            ['c_endpoint', 'in', 'range' => ['number', 'client']],
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