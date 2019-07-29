<?php

namespace sales\forms\api\communication\voice\defaults;

use sales\forms\CompositeForm;

/**
 * Class DefaultsForm
 * @property $c_id;
 * @property $c_call_status;
 * @property $c_endpoint;
 * @property $action;
 * @property $type;
 *
 * @property CallForm $call
 * @property CallDataForm $callData
 */
class DefaultsForm extends CompositeForm
{

    public $c_id;
    public $c_call_status;
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
            ['c_call_status', 'in', 'range' => ['no-answer', 'in-progress', 'completed']],
            ['c_endpoint', 'in', 'range' => ['client']],
            ['action', 'in', 'range' => ['update']],
            ['type', 'in', 'range' => ['voip']],
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