<?php

namespace frontend\widgets\nestedSets;

use yii\base\Model;
use yii\base\Widget;
use yii\db\ActiveQuery;
use yii\helpers\BaseHtml;
use yii\helpers\Json;

class NestedSetsWidget extends Widget
{
    public ?int $currentModelId = 0;
    public ?int $parentCategoryId = 0;
    public string $attribute = 'nestedSets';
    public string $placeholder = 'Select an option';
    public ?string $label;
    public Model $model;
    public ActiveQuery $query;

    public function init()
    {
        NestedSetsAsset::register($this->getView());
        parent::init();
    }

    public function run()
    {
        parent::run();
        $preparedData = $this->prepareData($this->query);

        return $this->render('nested_sets_tree', [
          'data'             => $preparedData,
          'currentModelId'   => $this->currentModelId,
          'parentCategoryId' => $this->parentCategoryId,
          'attribute'        => $this->attribute,
          'placeholder'      => $this->placeholder,
          'name'             => BaseHtml::getInputName($this->model, $this->attribute),
          'label'            => $this->label ?? BaseHtml::activeLabel($this->model, $this->attribute, ['class' => 'control-label']),
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

    private function findChildren($node): array
    {
        $nodeData = [
          'id'   => $node->cc_id,
          'text' => $node->cc_name,
        ];
        $children = $node->children(1)->all();
        if ($children) {
            foreach ($children as $child) {
                $nodeData['inc'][] = $this->findChildren($child);
            }
        }

        return $nodeData;
    }
}
