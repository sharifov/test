<?php

use common\models\CaseSale;
use src\services\caseSale\PnrPreparingService;
use yii\db\Migration;
use yii\helpers\Console;

/**
 * Class m220111_065042_correction_css_sale_pnr
 */
class m220111_065042_correction_css_sale_pnr extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $countUpdated = 0;
        $query = CaseSale::find()
            ->select(['css_cs_id', 'css_sale_id', 'css_sale_pnr'])
            ->andWhere(['>', 'LENGTH(css_sale_pnr)', 6])
            ->orderBy(['css_cs_id' => SORT_ASC])
            ->asArray()
            ->all();

        foreach ($query as $value) {
            $pnr = (new PnrPreparingService($value['css_sale_pnr']))->getPnr();
            CaseSale::updateAll(['css_sale_pnr' => $pnr], ['css_cs_id' => $value['css_cs_id'], 'css_sale_id' => $value['css_sale_id']]);
            $countUpdated++;
        }

        echo Console::renderColoredString('%g --- Updated (' . $countUpdated . ') pnr %n'), PHP_EOL;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220111_065042_correction_css_sale_pnr cannot be reverted.\n";
    }
}
