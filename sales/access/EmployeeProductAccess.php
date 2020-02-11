<?php

namespace sales\access;

use modules\product\src\entities\productType\ProductType;
use sales\helpers\user\UserFinder;
use yii\web\User;

/**
 * Class EmployeeProductAccess
 * @package sales\access
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
        $list = [];

        if ($this->user->can('product/manage/all')) {
            $list = ProductType::getEnabledList();
        } else {
            $employee = UserFinder::find($this->user->id);
            if ($employee->productType) {
                $list = $employee->productType
                    ->select(['pt_name', 'pt_id'])
                    ->orderBy(['pt_name' => SORT_ASC])
                    ->indexBy('pt_id')
                    ->asArray()
                    ->column();
            }
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
