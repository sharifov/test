<?php

namespace src\helpers\nestedSets;

use src\entities\cases\CaseCategory;

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
     * @param $ccId
     * @return string|null
     */
    public static function getCategoriesHierarchy($ccId): ?string
    {
        $parentsCategoriesHierarchy = null;
        $caseCategoryModel = CaseCategory::findOne($ccId);
        $parent = $caseCategoryModel->parents(1)->one();
        if ($parent) {
            $parentCategoryId = $parent->cc_id;
            $parentCategory = CaseCategory::findNestedSets()->andWhere(['cc_id' => $parent->cc_id])
                ->one();
            if ($parentCategory) {
                $parentCategoryName = $parentCategory->getAttribute('cc_name');
                $parentCategoryId = $parentCategory->getAttribute('cc_id');
            }
            $allParents = $caseCategoryModel->parents()->asArray()->all();
            if ($allParents) {
                $parentsNames = array_column($allParents, 'cc_name');
                $parentsCategoriesHierarchy = self::formatHierarchyString($parentsNames);
                $parentsCategoriesHierarchy .= ' / ' . $caseCategoryModel->getAttribute('cc_name');
            }
        } else {
            $parentsCategoriesHierarchy = $caseCategoryModel->getAttribute('cc_name');
        }
        return $parentsCategoriesHierarchy;
    }
}
