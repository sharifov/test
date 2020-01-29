<?php
/**
 * @var $this \yii\web\View
 * @var $product Product
 */

use modules\flight\src\helpers\FlightQuoteHelper;
use modules\product\src\entities\product\Product;
use yii\widgets\ListView;
use yii\widgets\Pjax;


$dataProvider = FlightQuoteHelper::generateDataProviderForQuoteList($product);
?>


<?php Pjax::begin(['id' => 'flight_quotes_list_' . $product->pr_id, 'timeout' => 2000, 'enablePushState' => false, 'enableReplaceState' => false]); ?>
<div class="x_panel">
    <div class="x_title">
        <h2><i class="fa fa-folder-o"></i> Quotes</h2>
        <ul class="nav navbar-right panel_toolbox">
            <li>
                <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
            </li>
        </ul>
        <div class="clearfix"></div>
    </div>
    <div class="x_content" style="display: block">

        <?= ListView::widget([
            'dataProvider' => $dataProvider,
            'itemView' => '_quote_item',
            'emptyText' => '<div class="text-center">Not found quotes</div><br>',
            //'layout' => "\n{items}<div class=\"text-center\">{pager}</div>\n", // {summary}\n<div class="text-center">{pager}</div>
            'viewParams' => [
//                'appliedQuote' => $lead->getAppliedAlternativeQuotes(),
//                'leadId' => $lead->id,
//                'leadForm' => $leadForm,
//                'isManager' => $is_manager,
            ],
            'itemOptions' => [
                //'class' => 'item',
                'tag' => false,
            ],
        ]);?>

    </div>
</div>
<?php Pjax::end() ?>
