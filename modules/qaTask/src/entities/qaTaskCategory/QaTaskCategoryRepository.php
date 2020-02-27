<?php

namespace modules\qaTask\src\entities\qaTaskCategory;

use modules\qaTask\src\exceptions\QaTaskCodeException;
use sales\repositories\NotFoundException;

/**
 * Class QaTaskCategoryRepository
 */
class QaTaskCategoryRepository
{
    public function find(int $id): QaTaskCategory
    {
        if ($category = QaTaskCategory::findOne($id)) {
            return $category;
        }
        throw new NotFoundException('Qa Task Category is not found', QaTaskCodeException::QA_TASK_CATEGORY_NOT_FOUND);
    }

    public function save(QaTaskCategory $category): int
    {
        if (!$category->save(false)) {
            throw new \RuntimeException('Saving error', QaTaskCodeException::QA_TASK_CATEGORY_SAVE);
        }
        return $category->tc_id;
    }

    public function remove(QaTaskCategory $category): void
    {
        if (!$category->delete()) {
            throw new \RuntimeException('Removing error', QaTaskCodeException::QA_TASK_CATEGORY_REMOVE);
        }
    }
}
