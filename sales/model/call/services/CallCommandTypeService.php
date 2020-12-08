<?php

namespace sales\model\call\services;

use sales\model\call\entity\callCommand\CallCommand;
use sales\model\call\entity\callCommand\types\CommandList;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\helpers\Url;

/**
 * Class CallCommandTypeService
 *
 * @property int $typeId
 * @property string $typeName
 */
class CallCommandTypeService
{
    public int $typeId;
    public ?string $typeName;

    private string $namespaceTypes = 'sales\model\call\entity\callCommand\types';
    private string $patchToTemplateDir = '@frontend/views/call-command/types/';
    private string $templateExtension = '.php';

    /**
     * @param int $typeId
     */
    public function __construct(int $typeId)
    {
        $this->typeId = $typeId;
        $this->checkType();
        $this->setTypeName();
    }

    /**
     * @return mixed
     */
    public function initTypeCommandClass()
    {
        $fullNameClass = $this->getFullClassName();
        if (class_exists($fullNameClass)) {
            return new $fullNameClass();
        }
        throw new \DomainException('Command Type "' . $fullNameClass . '" class is not initialized');
    }

    private function getFullClassName(): string
    {
        return $this->getNamespaceTypes() . '\\' . self::classNameFormatting($this->typeName);
    }

    public static function classNameFormatting(string $name): string
    {
        return Inflector::classify($name);
    }

    public function getPathToTemplateFile(): string
    {
        return $this->getPatchToTemplateDir() .
            self::viewNameFormatting($this->typeName) .
            $this->getTemplateExtension();
    }

    public static function viewNameFormatting(string $name): string
    {
        return Inflector::slug($name, '_');
    }

    public function checkTemplateFileExist(): bool
    {
        if (file_exists(\Yii::getAlias($this->getPathToTemplateFile()))) {
            return true;
        }
        throw new \DomainException('File: "' . $this->getPathToTemplateFile() . '" is not exist');
    }

    public function getNamespaceTypes(): string
    {
        return $this->namespaceTypes;
    }

    public function getPatchToTemplateDir(): string
    {
        return $this->patchToTemplateDir;
    }

    /**
     * @param $object
     * @param $data
     * @return mixed
     * @throws \Exception
     */
    public static function fillObject($object, $data)
    {
        foreach (get_object_vars($object) as $name => $value) {
            $object->{$name} = ArrayHelper::getValue($data, $name);
        }
        return $object;
    }

    /**
     * @param CommandList $object
     * @param $data
     * @return CommandList
     */
    public static function fillCommandList(CommandList $object, $data): CommandList
    {
        if (is_array($data) && !empty($data)) {
            foreach ($data as $key => $value) {
                if (is_string($key)) {
                    continue;
                }
                $data[$key]['sub_type'] = $value['typeId'];
                $data[$key]['sub_sort'] = $value['sort'];
                $data[$key]['model_id'] = $value['additional']['model_id'] ?? 0;
            }
            $object->setMultipleFormData($data);
        }
        return $object;
    }

    private function setTypeName(): CallCommandTypeService
    {
        $this->typeName = CallCommand::getTypeName($this->typeId);
        return $this;
    }

    private function checkType(): bool
    {
        if (ArrayHelper::isIn($this->typeId, array_keys(CallCommand::getTypeList()))) {
            return true;
        }
        throw new \InvalidArgumentException('TypeId is incorrect');
    }

    public function getTemplateExtension(): string
    {
        return $this->templateExtension;
    }

    public function getTypeName(): ?string
    {
        return $this->typeName;
    }

    /**
     * @param $formId
     * @param $typeId
     * @param bool $enableClientValidation
     * @param bool $enableAjaxValidation
     * @param bool $validateOnChange
     * @param bool $validateOnBlur
     * @return array
     */
    public static function configActiveForm(
        $formId,
        $typeId,
        $enableClientValidation = true,
        $enableAjaxValidation = false,
        $validateOnChange = true,
        $validateOnBlur = false
    ): array {
        return [
            'id' => $formId,
            'enableClientValidation' => $enableClientValidation,
            'enableAjaxValidation' => $enableAjaxValidation,
            'validateOnChange' => $validateOnChange,
            'validateOnBlur' => $validateOnBlur,
            'validationUrl' => Url::to(['call-command/validate-type-form', 'type_id' => $typeId]),
        ];
    }
}
