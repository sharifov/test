<?php

namespace frontend\widgets\nestedSets;

use RuntimeException;
use yii\base\Model;
use yii\base\Widget;
use yii\db\ActiveQuery;
use yii\helpers\BaseHtml;
use yii\helpers\Json;

class NestedSetsWidget extends Widget
{
    public ?int $currentModelId = 0;
    public ?string $parentCategoryId = '';
    public string $attribute = 'parentCategoryId';
    public string $placeholder = 'Select an option';
    public ?string $label;
    public ?Model $model = null;
    public ActiveQuery $query;
    public ?bool $allowToSelectEnabled = false;

    public function init()
    {
        NestedSetsAsset::register($this->getView());
        parent::init();
    }

    /**
     * @throws \Exception
     */
    public function run()
    {
        if (!$this->model) {
            throw new RuntimeException('Instance of a Model is required to run NestedSets widget. ');
        }
        parent::run();
        $preparedData = $this->prepareData($this->query);

        return $this->render('nested_sets_tree', [
            'data' => $preparedData,
            'currentModelId' => $this->currentModelId,
            'parentCategoryId' => $this->parentCategoryId,
            'attribute' => $this->attribute,
            'placeholder' => $this->placeholder,
            'name' => BaseHtml::getInputName($this->model, $this->attribute),
            'label' => $this->label ?? BaseHtml::activeLabel($this->model, $this->attribute, ['class' => 'control-label']),
        ]);
    }

    private function prepareData($query): string
    {
        $nestedSetData = [];

        if ($query) {
            $roots = $query->roots()->all();
            foreach ($roots as $root) {
                $nestedSetData[] = $this->findChildren($root);
            }
        }

        return Json::encode($nestedSetData);
    }

    /**
     * Generate data structure for js extension. To make option disabled put empty string to id(value)
     * @param $node
     * @param bool $childrenLock
     * @return array
     */
    private function findChildren($node, bool $childrenLock = false): array
    {
        $nodeData = [
            'id' => $node->cc_id,
            'text' => $node->cc_name,
        ];
        if ($this->allowToSelectEnabled) {
            /*check node has parent equal current model. if so - such node has to be disabled also*/
            if (isset($this->currentModelId) && $node->cc_id === $this->currentModelId) {
                $childrenLock = true;
            }
            $this->setDisabled($nodeData, $node, $childrenLock);
        }
        $children = $node->children(1)->all();
        if ($children) {
            foreach ($children as $child) {
                $nodeData['inc'][] = $this->findChildren($child, $childrenLock);
            }
        }

        return $nodeData;
    }

    /**
     * Check if allow_to_select attribute is set. If not - option is disabled
     * @param array $nodeData
     * @param \yii\base\Model $node
     * @param bool $mandatoryDisableOption
     * @return void
     */
    private function setDisabled(array &$nodeData, Model $node, bool $mandatoryDisableOption): void
    {
        if (!$node->cc_allow_to_select || $mandatoryDisableOption) {
            $nodeData['id'] = '';
        }
    }
}
