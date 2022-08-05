<?php

use modules\abac\src\AbacService;
use yii\db\Migration;

/**
 * Class m220803_073733_add_abac_permissions_for_client_chat_form_response_table
 */
class m220803_073733_add_abac_permissions_for_client_chat_form_response_table extends Migration
{
    public $dump = 'eyJhcF9pZCI6MzU4LCJhcF9ydWxlX3R5cGUiOiJwIiwiYXBfc3ViamVjdCI6IihcImFkbWluXCIgaW4gci5zdWIuZW52LnVzZXIucm9sZXMgfHwgXCJzdXBlcmFkbWluXCIgaW4gci5zdWIuZW52LnVzZXIucm9sZXMpIiwiYXBfc3ViamVjdF9qc29uIjoie1wiY29uZGl0aW9uXCI6XCJBTkRcIixcInJ1bGVzXCI6W3tcImlkXCI6XCJlbnZfdXNlcl9tdWx0aV9yb2xlc1wiLFwiZmllbGRcIjpcImVudi51c2VyLnJvbGVzXCIsXCJ0eXBlXCI6XCJzdHJpbmdcIixcImlucHV0XCI6XCJzZWxlY3RcIixcIm9wZXJhdG9yXCI6XCJjb250YWluc1wiLFwidmFsdWVcIjpbXCJhZG1pblwiLFwic3VwZXJhZG1pblwiXX1dLFwibm90XCI6ZmFsc2UsXCJ2YWxpZFwiOnRydWV9IiwiYXBfb2JqZWN0IjoiY2xpZW50LWNoYXQvY2xpZW50LWNoYXQvY2xpZW50LWNoYXQtZnJvbSIsImFwX2FjdGlvbiI6IihhY2Nlc3MpIiwiYXBfYWN0aW9uX2pzb24iOiJbXCJhY2Nlc3NcIl0iLCJhcF9lZmZlY3QiOjEsImFwX3RpdGxlIjoiQWNjZXNzIHRvIHRoZSBjbGllbnQgY2hhdCBmb3JtIG9uIHRoZSBjbGllbnQgY2hhdCBwYWdlIiwiYXBfc29ydF9vcmRlciI6NTAsImFwX2NyZWF0ZWRfZHQiOiIyMDIyLTA4LTA1IDA5OjI1OjAxIiwiYXBfdXBkYXRlZF9kdCI6IjIwMjItMDgtMDUgMDk6MjU6MDEiLCJhcF9jcmVhdGVkX3VzZXJfaWQiOm51bGwsImFwX3VwZGF0ZWRfdXNlcl9pZCI6bnVsbCwiYXBfZW5hYmxlZCI6MSwiYXBfaGFzaF9jb2RlIjoiMjFkNTQyMWQ4MiJ9';

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
