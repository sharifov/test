<?php

namespace sales\model\lead\useCases\lead\import;

/**
 * Class Parse
 *
 * @property array $errors
 * @property LeadImportForm[] $forms
 */
class Parse
{
    private $errors;
    private $forms;

    /**
     * Parsing constructor.
     * @param array $errors
     * @param LeadImportForm[] $forms
     */
    public function __construct(array $errors, array $forms)
    {
        $this->errors = $errors;
        $this->forms = $forms;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @return LeadImportForm[]
     */
    public function getForms(): array
    {
        return $this->forms;
    }
}
