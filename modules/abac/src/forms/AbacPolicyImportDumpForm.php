<?php

namespace modules\abac\src\forms;

use modules\abac\src\AbacService;
use modules\abac\src\entities\AbacPolicy;
use yii\base\Model;

/**
 * This is the AbacPolicyImportDumpForm class
 *
 * @property string $dump
 * @property bool $enabled
 * @property AbacPolicy $policyModel
 */
class AbacPolicyImportDumpForm extends Model
{
    public $dump;
    public $enabled;
    public $policyModel;

    public function rules(): array
    {
        return [
            [['dump'], 'required'],
            [['dump'], 'string'],
            [['enabled'], 'boolean'],
            [['dump'], 'validatePolicyDump'],
        ];
    }

    /**
     * @return AbacPolicy|null
     */
    public function getPolicyModel(): ?AbacPolicy
    {
        return $this->policyModel;
    }

    public function validatePolicyDump($attribute, $params)
    {
        if (!$this->hasErrors()) {
            try {
                $this->policyModel = AbacService::convertDumpToObject($this->dump);
            } catch (\Throwable $throwable) {
                $this->addError($attribute, 'Error Dump: ' . $throwable->getMessage());
            }
        }
    }

    /**
     * @return string[]
     */
    public function attributeLabels(): array
    {
        return [
            'dump' => 'Policy dump',
            'enabled' => 'Enabled',
        ];
    }
}
