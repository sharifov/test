<?php

use modules\abac\src\AbacService;
use yii\db\Migration;

/**
 * Class m220816_153650_add_abac_permission_create_client_chat_form_response_table
 */
class m220816_153650_add_abac_permission_create_client_chat_form_response_table extends Migration
{
    public $dump = 'eyJhcF9pZCI6MzY0LCJhcF9ydWxlX3R5cGUiOiJwIiwiYXBfc3ViamVjdCI6IihcImFkbWluXCIgaW4gci5zdWIuZW52LnVzZXIucm9sZXMgfHwgXCJzdXBlcmFkbWluXCIgaW4gci5zdWIuZW52LnVzZXIucm9sZXMpIiwiYXBfc3ViamVjdF9qc29uIjoie1wiY29uZGl0aW9uXCI6XCJBTkRcIixcInJ1bGVzXCI6W3tcImlkXCI6XCJlbnZfdXNlcl9tdWx0aV9yb2xlc1wiLFwiZmllbGRcIjpcImVudi51c2VyLnJvbGVzXCIsXCJ0eXBlXCI6XCJzdHJpbmdcIixcImlucHV0XCI6XCJzZWxlY3RcIixcIm9wZXJhdG9yXCI6XCJjb250YWluc1wiLFwidmFsdWVcIjpbXCJhZG1pblwiLFwic3VwZXJhZG1pblwiXX1dLFwibm90XCI6ZmFsc2UsXCJ2YWxpZFwiOnRydWV9IiwiYXBfb2JqZWN0IjoiY2xpZW50LWNoYXQvY2xpZW50LWNoYXQvdWkvY2xpZW50LWNoYXQtZnJvbSIsImFwX2FjdGlvbiI6IihjcmVhdGUpIiwiYXBfYWN0aW9uX2pzb24iOiJbXCJjcmVhdGVcIl0iLCJhcF9lZmZlY3QiOjEsImFwX3RpdGxlIjoiQWNjZXNzIHRvIGNyZWF0ZSBjbGllbnQgY2hhdCBmb3JtIG9uIGNsaWVudCBjaGF0IHBhZ2UiLCJhcF9zb3J0X29yZGVyIjo1MCwiYXBfY3JlYXRlZF9kdCI6IjIwMjItMDgtMTYgMTU6MzQ6NDAiLCJhcF91cGRhdGVkX2R0IjoiMjAyMi0wOC0xNiAxNTozNToyMSIsImFwX2NyZWF0ZWRfdXNlcl9pZCI6Njc1LCJhcF91cGRhdGVkX3VzZXJfaWQiOjY3NSwiYXBfZW5hYmxlZCI6MSwiYXBfaGFzaF9jb2RlIjoiYzVmZmU5OTE4NiJ9';

    /**
     * @return false|mixed|void
     */
    public function safeUp()
    {
        echo AbacService::importPolicyFromDump($this->dump, true) ? '- OK: added' : '- Error: not added';
        echo PHP_EOL;
    }


    /**
     * @return false|mixed|void
     */
    public function safeDown()
    {
        echo AbacService::removePolicyFromDump($this->dump) ? '- OK: removed' : '- Error: not removed';
        echo PHP_EOL;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220803_073733_add_rbac_permissions_for_client_chat_form_response_table cannot be reverted.\n";

        return false;
    }
    */
}
