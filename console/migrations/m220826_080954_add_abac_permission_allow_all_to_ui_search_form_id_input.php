<?php

use modules\abac\src\AbacService;
use yii\db\Migration;

/**
 * Class m220826_080954_add_abac_permission_allow_all_to_ui_search_form_id_input
 */
class m220826_080954_add_abac_permission_allow_all_to_ui_search_form_id_input extends Migration
{
    public $dump = 'eyJhcF9pZCI6NDAwLCJhcF9ydWxlX3R5cGUiOiJwIiwiYXBfc3ViamVjdCI6IihyLnN1Yi5lbnYuYXZhaWxhYmxlID09IHRydWUpIiwiYXBfc3ViamVjdF9qc29uIjoie1wiY29uZGl0aW9uXCI6XCJBTkRcIixcInJ1bGVzXCI6W3tcImlkXCI6XCJlbnZfYXZhaWxhYmxlXCIsXCJmaWVsZFwiOlwiZW52LmF2YWlsYWJsZVwiLFwidHlwZVwiOlwiYm9vbGVhblwiLFwiaW5wdXRcIjpcInJhZGlvXCIsXCJvcGVyYXRvclwiOlwiPT1cIixcInZhbHVlXCI6dHJ1ZX1dLFwibm90XCI6ZmFsc2UsXCJ2YWxpZFwiOnRydWV9IiwiYXBfb2JqZWN0IjoibGVhZC9sZWFkL3NlYXJjaF9mb3JtL2lkX2lucHV0IiwiYXBfYWN0aW9uIjoiKGFjY2VzcykiLCJhcF9hY3Rpb25fanNvbiI6IltcImFjY2Vzc1wiXSIsImFwX2VmZmVjdCI6MSwiYXBfdGl0bGUiOiJBY2Nlc3MgdG8gTGVhZCBJRCBpbnB1dCBpbiBzZWFyY2ggZm9ybSIsImFwX3NvcnRfb3JkZXIiOjUwLCJhcF9jcmVhdGVkX2R0IjoiMjAyMi0wOC0yNSAyMjo0NDo1MyIsImFwX3VwZGF0ZWRfZHQiOiIyMDIyLTA4LTI2IDA0OjA3OjIzIiwiYXBfY3JlYXRlZF91c2VyX2lkIjozMDIsImFwX3VwZGF0ZWRfdXNlcl9pZCI6MzAyLCJhcF9lbmFibGVkIjoxLCJhcF9oYXNoX2NvZGUiOiJjYmVkNDg2ZTU3In0=';

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
        echo "m220826_080954_add_abac_permission_allow_all_to_ui_search_form_id_input cannot be reverted.\n";

        return false;
    }
    */
}
