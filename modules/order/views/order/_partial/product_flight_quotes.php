<?php

use yii\widgets\ListView;
use yii\data\ArrayDataProvider;
use yii\widgets\Pjax;

/**
 * @var $data array
 * @var $productId int
 */

$dataProvider = new ArrayDataProvider([
    'allModels' => array_reverse($data),
    'pagination' => [
        'pageSize' => 1,
    ],
]);

?>
<?php Pjax::begin(['id' => 'pjax-product-quote-list-' . $productId, 'timeout' => 5000, 'enablePushState' => false, 'enableReplaceState' => false]); ?>
<div class="x_panel">
    <div class="x_title">
        <h2><i class="fa fa-folder-o"></i> Flight Quotes
            <?php if ($dataProvider->totalCount) : ?>
                <sup>(<?=$dataProvider->totalCount?>)</sup>
            <?php endif; ?>
        </h2>
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
            'itemView' => 'product_flight_quote_item',
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