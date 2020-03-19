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
     * @param int $id
     * @return CaseCategory
     */
    public function find(int $id): CaseCategory
    {
        if ($category = CaseCategory::findOne($id)) {
            return $category;
        }
        throw new NotFoundException('Case category is not found');
    }

    /**
     * @param CaseCategory $category
     * @return int
     */
    public function save(CaseCategory $category): int
    {
        if (!$category->save(false)) {
            throw new \RuntimeException('Saving error');
        }
        return $category->cc_id;
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