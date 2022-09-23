<?php

namespace src\helpers\nestedSets;

use src\entities\cases\CaseCategory;
use yii\base\Model;
use yii\helpers\Json;

class NestedSetsHelper
{
    /**
     * @param array $parentsNames
     * @param string $delimiter
     * @return string
     */
    public static function formatHierarchyString(array $parentsNames, string $delimiter = ' / '): string
    {
        $parentsNamesString = implode($delimiter, $parentsNames);

        return $parentsNamesString;
    }

    /**
     * Generate Categories Hierarchy string. The Last Category is Case's category. If no parents - then only case category shown.
     * @param int|null $ccId
     * @return string|null
     */
    public static function getCategoriesHierarchy(?int $ccId): ?string
    {
        $parentsCategoriesHierarchy = '(not set)';
        $caseCategoryModel = CaseCategory::findOne($ccId);
        if ($caseCategoryModel) {
            $parent = $caseCategoryModel->parents(1)->one();
            if ($parent) {
                $parentCategoryId = $parent->cc_id;
                $parentCategory = CaseCategory::findNestedSets()->andWhere(['cc_id' => $parent->cc_id])
                    ->one();
                if ($parentCategory) {
                    $parentCategoryName = $parentCategory->cc_name;
                    $parentCategoryId = $parentCategory->cc_id;
                }
                $allParents = $caseCategoryModel->parents()->asArray()->all();
                if ($allParents) {
                    $parentsNames = array_column($allParents, 'cc_name');
                    $parentsCategoriesHierarchy = self::formatHierarchyString($parentsNames);
                    $parentsCategoriesHierarchy .= ' / ' . $caseCategoryModel->cc_name;
                }
            } else {
                $parentsCategoriesHierarchy = $caseCategoryModel->cc_name;
            }
        }
        return $parentsCategoriesHierarchy;
    }

    public static function jsonData($query): string
    {
        $nestedSetData = [];
        if ($query) {
            $roots = $query->roots()->all();
            foreach ($roots as $root) {
                $nestedSetData[] = self::findChildren($root);
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
    private static function findChildren($node, bool $childrenLock = false): array
    {
        $nodeData = [
            'id' => $node->cc_id,
            'text' => $node->cc_name,
        ];
        self::setDisabled($nodeData, $node, $childrenLock);
        $children = $node->children(1)->all();
        if ($children) {
            foreach ($children as $child) {
                $nodeData['inc'][] = self::findChildren($child, $childrenLock);
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
    private static function setDisabled(array &$nodeData, Model $node, bool $mandatoryDisableOption): void
    {
        if (!$node->cc_allow_to_select || $mandatoryDisableOption) {
            $nodeData['id'] = '';
        }
    }
}
