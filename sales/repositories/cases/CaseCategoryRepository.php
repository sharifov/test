<?php

namespace sales\repositories\cases;

use sales\entities\cases\CaseCategory;
use sales\repositories\NotFoundException;

class CaseCategoryRepository
{

    /**
     * @param int $depId
     * @return CaseCategory[]
     */
    public function getAllByDep(int $depId): array
    {
        return CaseCategory::find()->andWhere(['cc_dep_id' => $depId])->orderBy(['cc_created_dt' => SORT_ASC])->all();
    }

    /**
     * @param string $key
     * @return CaseCategory
     */
    public function findByKey(string $key): CaseCategory
    {
        if ($category = CaseCategory::find()->andWhere(['cc_key' => $key])->limit(1)->one()) {
            return $category;
        }
        throw new NotFoundException('Case category is not found');
    }

    /**
     * @param CaseCategory $category
     * @return string
     */
    public function save(CaseCategory $category): string
    {
        if (!$category->save(false)) {
            throw new \RuntimeException('Saving error');
        }
        return $category->cc_key;
    }

    /**
     * @param CaseCategory $category
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function remove(CaseCategory $category): void
    {
        if (!$category->delete()) {
            throw new \RuntimeException('Removing error');
        }
    }
}