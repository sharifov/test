<?php

namespace modules\order\src\entities\order;

use modules\fileStorage\src\entity\fileOrder\FileOrder;

class OrderFiles
{
    public function getList(Order $order): array
    {
        $files = [];
        $filesOrders = FileOrder::find()->andWhere(['fo_or_id' => $order->or_id])->all();
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
