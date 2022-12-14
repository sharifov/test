<?php

namespace src\access;

use modules\product\src\entities\productType\ProductType;
use yii\web\User;

/**
 * Class EmployeeProductAccess
 * @package src\access
 */
class EmployeeProductAccess
{
    private $user;

    /**
     * EmployeeProductAccess constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return array
     */
    public function getProductList(): array
    {
        if ($this->user->can('product/manage/all')) {
            $list = ProductType::getEnabledList();
        } else {
            $list = ProductType::find()
                ->select(['pt_name', 'pt_id'])
                ->innerJoin('user_product_type', 'user_product_type.upt_product_type_id = product_type.pt_id')
                ->where(['upt_user_id' => $this->user->id])
                //->orderBy(['pt_name' => SORT_ASC])
                ->orderBy(['pt_sort_order' => SORT_ASC])
                ->indexBy('pt_id')
                ->asArray()
                ->column();
        }
        return $list ?? [];
    }

    /**
     * @return array
     */
    public function getProductItemList(): array
    {
        if ($this->user->can('product/manage/all')) {
            $list = ProductType::getEnabledItemList();
        } else {
            $list = ProductType::find()
                ->select(['pt_name', 'pt_id', 'pt_icon_class'])
                ->innerJoin('user_product_type', 'user_product_type.upt_product_type_id = product_type.pt_id')
                ->where(['upt_user_id' => $this->user->id])
                ->orderBy(['pt_sort_order' => SORT_ASC])
                //->indexBy('pt_id')
                ->asArray()
                ->all();
        }
        return $list ?? [];
    }

    /**
     * @return array
     */
    public function getProductListId(): array
    {
        return array_keys($this->getProductList());
    }
}
