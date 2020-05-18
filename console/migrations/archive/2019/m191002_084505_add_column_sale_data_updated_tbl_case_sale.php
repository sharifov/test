<?php

use common\models\CaseSale;
use yii\db\Migration;

/**
 * Class m191002_084505_add_column_sale_data_updated_tbl_case_sale
 */
class m191002_084505_add_column_sale_data_updated_tbl_case_sale extends Migration
{
	/**
	 * {@inheritdoc}
	 * @throws \yii\base\Exception
	 */
    public function safeUp()
    {
		$this->addColumn('{{%case_sale}}', 'css_sale_data_updated', $this->json());

		$createTrigger = <<<SQL
CREATE TRIGGER `case_sale_BEFORE_INSERT` BEFORE INSERT ON `case_sale` FOR EACH ROW BEGIN
	set new.css_sale_data_updated = new.css_sale_data;
END
SQL;

		$this->execute($createTrigger);


		Yii::$app->db->getSchema()->refreshTableSchema('{{%case_sale}}');


		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}

		$caseSales = CaseSale::find()->all();

		foreach ($caseSales as $sale) {
			$sale->css_sale_data_updated = $sale->css_sale_data;
			$sale->save();
		}
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$this->execute('DROP TRIGGER IF EXISTS `case_sale_BEFORE_INSERT`');

		$this->dropColumn('{{%case_sale}}', 'css_sale_data_updated');

		Yii::$app->db->getSchema()->refreshTableSchema('{{%case_sale}}');

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}
    }
}
