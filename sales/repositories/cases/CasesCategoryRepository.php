<?php

namespace sales\repositories\cases;

use sales\entities\cases\CasesCategory;
use sales\repositories\NotFoundException;

class CasesCategoryRepository
{

    /**
     * @param int $depId
     * @return CasesCategory[]
     */
    public function getAllByDep(int $depId): array
    {
        return CasesCategory::find()->andWhere(['cc_dep_id' => $depId])->orderBy(['cc_created_dt' => SORT_ASC])->all();
    }

    /**
     * @param string $key
     * @return CasesCategory
     */
    public function findByKey(string $key): CasesCategory
    {
        if ($category = CasesCategory::find()->andWhere(['cc_key' => $key])->limit(1)->one()) {
            return $category;
        }
        throw new NotFoundException('Case category is not found');
    }

    /**
     * @param CasesCategory $category
     * @return string
     */
    public function save(CasesCategory $category): string
    {
        if (!$category->save(false)) {
            throw new \RuntimeException('Saving error');
        }
        return $category->cc_key;
    }

    /**
     * @param CasesCategory $category
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function remove(CasesCategory $category): void
    {
        if (!$category->delete()) {
            throw new \RuntimeException('Removing error');
        }
    }
}