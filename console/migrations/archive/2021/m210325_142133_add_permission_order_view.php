<?php

use yii\db\Migration;

/**
 * Class m210325_142133_add_permission_order_view
 */
class m210325_142133_add_permission_order_view extends Migration
{
    public $routes = [
        '/order/order/view',
    ];

    public $roles = [
        \common\models\Employee::ROLE_ADMIN,
        \common\models\Employee::ROLE_SUPER_ADMIN,
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $viewOrder = $auth->createPermission('order/view/order');
        $viewOrder->description = 'View order info';
        $auth->add($viewOrder);

        $viewFiles = $auth->createPermission('order/view/file');
        $viewFiles->description = 'View order files';
        $auth->add($viewFiles);

        $viewAdditional = $auth->createPermission('order/view/additionalInfo');
        $viewAdditional->description = 'View order additional info';
        $auth->add($viewAdditional);

        $viewInvoice = $auth->createPermission('order/view/invoice');
        $viewInvoice->description = 'View order invoice';
        $auth->add($viewInvoice);

        $viewPayment = $auth->createPermission('order/view/payment');
        $viewPayment->description = 'View order payment';
        $auth->add($viewPayment);

        $viewBillingInfo = $auth->createPermission('order/view/billingInfo');
        $viewBillingInfo->description = 'View order billing info';
        $auth->add($viewBillingInfo);

        foreach ($this->roles as $item) {
            if ($role = $auth->getRole($item)) {
                $auth->addChild($role, $viewOrder);
                $auth->addChild($role, $viewFiles);
                $auth->addChild($role, $viewAdditional);
                $auth->addChild($role, $viewInvoice);
                $auth->addChild($role, $viewPayment);
                $auth->addChild($role, $viewBillingInfo);
            }
        }

        (new \console\migrations\RbacMigrationService())->up($this->routes, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        $permissions = [
            'order/view/order',
            'order/view/file',
            'order/view/additionalInfo',
            'order/view/invoice',
            'order/view/payment',
            'order/view/billingInfo',
        ];

        foreach ($permissions as $permissionName) {
            if ($permission = $auth->getPermission($permissionName)) {
                $auth->remove($permission);
            }
        }

        (new \console\migrations\RbacMigrationService())->down($this->routes, $this->roles);
    }
}
