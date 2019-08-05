<?php

namespace sales\forms\api\communication\voice\incoming;

use sales\forms\CompositeForm;

/**
 * Class IncomingForm
 * @property $type
 * @property $call_id
 *
 * @property CallForm $call
 */
class IncomingForm extends CompositeForm
{

    public $type;
    public $call_id;

    public function __construct($config = [])
    {
        $this->call = new CallForm();
        parent::__construct($config);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['type', 'in', 'range' => ['voip_incoming']],
            ['call_id', 'integer']
        ];
    }

    /**
     * @return array
     */
    public function internalForms(): array
    {
        return ['call'];
    }

}