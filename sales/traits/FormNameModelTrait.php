<?php

namespace sales\traits;

/**
 * Trait FormNameModelTrait
 */
trait FormNameModelTrait
{
    protected $_formName;

    public function formName()
    {
        return $this->_formName ?: parent::formName();
    }

    public function setFormName($name)
    {
        $this->_formName = $name;
    }
}
