<?php

namespace modules\abac\migrations;

use modules\abac\src\AbacService;
use yii\db\Migration;

/**
 * Class m220816_151904_add_abac_permission_to_view_origin_data_from_bo_voluntary_refund
 */
class m220816_151904_add_abac_permission_to_view_origin_data_from_bo_voluntary_refund extends Migration
{
    public string $dump = 'eyJhcF9pZCI6NDQwLCJhcF9ydWxlX3R5cGUiOiJwIiwiYXBfc3ViamVjdCI6IihcImV4X2FnZW50XCIgbm90IGluIHIuc3ViLmVudi51c2VyLnJvbGVzKSAmJiAoXCJjbGllbnRfY2hhdF9hZ2VudF9leHBlcnRcIiBub3QgaW4gci5zdWIuZW52LnVzZXIucm9sZXMpIiwiYXBfc3ViamVjdF9qc29uIjoie1wiY29uZGl0aW9uXCI6XCJBTkRcIixcInJ1bGVzXCI6W3tcImlkXCI6XCJlbnZfdXNlcl9yb2xlc1wiLFwiZmllbGRcIjpcImVudi51c2VyLnJvbGVzXCIsXCJ0eXBlXCI6XCJzdHJpbmdcIixcImlucHV0XCI6XCJzZWxlY3RcIixcIm9wZXJhdG9yXCI6XCJub3RfaW5fYXJyYXlcIixcInZhbHVlXCI6XCJleF9hZ2VudFwifSx7XCJpZFwiOlwiZW52X3VzZXJfcm9sZXNcIixcImZpZWxkXCI6XCJlbnYudXNlci5yb2xlc1wiLFwidHlwZVwiOlwic3RyaW5nXCIsXCJpbnB1dFwiOlwic2VsZWN0XCIsXCJvcGVyYXRvclwiOlwibm90X2luX2FycmF5XCIsXCJ2YWx1ZVwiOlwiY2xpZW50X2NoYXRfYWdlbnRfZXhwZXJ0XCJ9XSxcIm5vdFwiOmZhbHNlLFwidmFsaWRcIjp0cnVlfSIsImFwX29iamVjdCI6InByb2R1Y3QvcHJvZHVjdC1xdW90ZS9vYmovcHJvZHVjdC1xdW90ZSIsImFwX2FjdGlvbiI6Iih2aWV3Vm9sdW50YXJ5UmVmdW5kT3JpZ2luRGF0YUZyb21CbykiLCJhcF9hY3Rpb25fanNvbiI6IltcInZpZXdWb2x1bnRhcnlSZWZ1bmRPcmlnaW5EYXRhRnJvbUJvXCJdIiwiYXBfZWZmZWN0IjoxLCJhcF90aXRsZSI6IlZpZXcgT3JpZ2luIERhdGEgRnJvbSBCTyAoVm9sdW50YXJ5IFJlZnVuZCkiLCJhcF9zb3J0X29yZGVyIjo1MCwiYXBfY3JlYXRlZF9kdCI6IjIwMjItMDgtMTcgMDc6NTM6MDciLCJhcF91cGRhdGVkX2R0IjoiMjAyMi0wOC0xNyAwODozMDo0NiIsImFwX2NyZWF0ZWRfdXNlcl9pZCI6bnVsbCwiYXBfdXBkYXRlZF91c2VyX2lkIjoxLCJhcF9lbmFibGVkIjoxLCJhcF9oYXNoX2NvZGUiOiJhYjEyODgwYzE2In0=';

    /**
     * @return void
     */
    public function safeUp()
    {
        echo AbacService::importPolicyFromDump($this->dump, true) ? '- OK: added' : '- Error: not added';
        echo PHP_EOL;
    }

    /**
     * @return void
     */
    public function safeDown()
    {
        echo AbacService::removePolicyFromDump($this->dump) ? '- OK: removed' : '- Error: not removed';
        echo PHP_EOL;
    }
}
