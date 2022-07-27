<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use modules\abac\src\AbacService;
use yii\db\Migration;

/**
 * Class m220726_122758_add_abac_policy_my_task_list
 */
class m220726_122758_add_abac_policy_my_task_list extends Migration
{
    public string $dump = 'eyJhcF9pZCI6NDAyLCJhcF9ydWxlX3R5cGUiOiJwIiwiYXBfc3ViamVjdCI6IihcImFkbWluXCIgaW4gci5zdWIuZW52LnVzZXIucm9sZXMgfHwgXCJzdXBlcmFkbWluXCIgaW4gci5zdWIuZW52LnVzZXIucm9sZXMpIiwiYXBfc3ViamVjdF9qc29uIjoie1wiY29uZGl0aW9uXCI6XCJBTkRcIixcInJ1bGVzXCI6W3tcImlkXCI6XCJlbnZfdXNlcl9tdWx0aV9yb2xlc1wiLFwiZmllbGRcIjpcImVudi51c2VyLnJvbGVzXCIsXCJ0eXBlXCI6XCJzdHJpbmdcIixcImlucHV0XCI6XCJzZWxlY3RcIixcIm9wZXJhdG9yXCI6XCJjb250YWluc1wiLFwidmFsdWVcIjpbXCJhZG1pblwiLFwic3VwZXJhZG1pblwiXX1dLFwibm90XCI6ZmFsc2UsXCJ2YWxpZFwiOnRydWV9IiwiYXBfb2JqZWN0IjoidGFzay1saXN0L3Rhc2stbGlzdC9hY3QvbXlfdGFza19saXN0IiwiYXBfYWN0aW9uIjoiKGFjY2VzcykiLCJhcF9hY3Rpb25fanNvbiI6IltcImFjY2Vzc1wiXSIsImFwX2VmZmVjdCI6MSwiYXBfdGl0bGUiOiJCYXNlIEFjY2VzcyBmb3IgTXkgVGFzayBMaXN0IHBhZ2UiLCJhcF9zb3J0X29yZGVyIjo1MCwiYXBfY3JlYXRlZF9kdCI6IjIwMjItMDctMjYgMTI6MjU6NTUiLCJhcF91cGRhdGVkX2R0IjoiMjAyMi0wNy0yNiAxMjoyNTo1NSIsImFwX2NyZWF0ZWRfdXNlcl9pZCI6MTY3LCJhcF91cGRhdGVkX3VzZXJfaWQiOjE2NywiYXBfZW5hYmxlZCI6MSwiYXBfaGFzaF9jb2RlIjoiOWFjMjI1M2U3YyJ9';

    private array $routes = [
        '/task-list/index'
    ];

    private array $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN,
        Employee::ROLE_QA_DEV,
        Employee::ROLE_DEV,
        Employee::ROLE_SALES_SENIOR,
        Employee::ROLE_AGENT,
    ];

    /**
     * @return void
     * @throws \yii\base\Exception
     */
    public function safeUp()
    {
        (new RbacMigrationService())->up($this->routes, $this->roles);
        echo AbacService::importPolicyFromDump($this->dump, true) ? '- OK: added' : '- Error: not added';
        echo PHP_EOL;
    }


    /**
     * @return void
     */
    public function safeDown()
    {
        (new RbacMigrationService())->down($this->routes, $this->roles);
        echo AbacService::removePolicyFromDump($this->dump) ? '- OK: removed' : '- Error: not removed';
        echo PHP_EOL;
    }
}
