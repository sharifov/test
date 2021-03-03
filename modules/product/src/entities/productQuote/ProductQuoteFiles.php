<?php

namespace modules\product\src\entities\productQuote;

use modules\fileStorage\src\entity\fileOrder\FileOrder;

class ProductQuoteFiles
{
    public function getList(ProductQuote $quote): array
    {
        $files = [];
        $filesOrders = FileOrder::find()->andWhere(['fo_pq_id' => $quote->pq_id])->all();
        if (!$filesOrders) {
            return $files;
        }
        /** @var FileOrder[] $filesOrders */
        foreach ($filesOrders as $file) {
            $files[] = [
                'name' => $file->file->fs_name,
                'uid' => $file->file->fs_uid,
                'categoryId' => $file->fo_category_id,
                'categoryName' => FileOrder::CATEGORY_LIST[$file->fo_category_id] ?? 'undefined',
            ];
        }
        return $files;
    }
}
