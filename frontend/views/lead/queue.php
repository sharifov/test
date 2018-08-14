<?php
/**
 * @var $this \yii\web\View
 * @var $dataProvider ActiveDataProvider[]
 * @var $searchModel Lead
 */


use yii\data\ActiveDataProvider;
use common\models\Lead;

$template = <<<HTML
<div class="table-pagination">
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <div class="table-pagination__entries-num-text">
                    {summary}
                </div>
            </div>
            <div class="col-sm-6">
                <div class="table-pagination__pagination-wrap">
                    {pager}
                </div>
            </div>
        </div>
    </div>
</div>
{items}
HTML;

$this->registerJsFile('/js/moment.min.js', [
    'position' => \yii\web\View::POS_HEAD,
    'depends' => [
        \yii\web\JqueryAsset::className()
    ]
]);
$this->registerJsFile('/js/moment-timezone-with-data.min.js', [
    'position' => \yii\web\View::POS_HEAD,
    'depends' => [
        \yii\web\JqueryAsset::className()
    ]
]);
$this->registerJsFile('/js/jquery.countdown-2.2.0/jquery.countdown.min.js', [
    'position' => \yii\web\View::POS_HEAD,
    'depends' => [
        \yii\web\JqueryAsset::className()
    ]
]);

if (!is_array($dataProvider)) {
    echo $this->render('partial/_queueGrid', [
        'template' => $template,
        'dataProvider' => $dataProvider,
        'searchModel' => $searchModel,
        'div' => ''
    ]);
} else {
    foreach ($dataProvider as $div => $dataProv) :
        $collapse = ($div == Lead::DIV_GRID_WITH_OUT_EMAIL);
        ?>
        <div class="panel panel-main">
            <div class="panel panel-primary mb-0">
                <div class="panel-heading collapsing-heading">
                    <a data-toggle="collapse" href="#sale-queue-<?= $div ?>"
                       class="collapsing-heading__collapse-link <?= ($collapse) ? '' : 'collapsed' ?>"
                       aria-expanded="<?= ($collapse) ? 'true' : 'false' ?>">
                        <?= sprintf('%s (%d)', Lead::getDivs($div), $dataProv->totalCount) ?>
                        <i class="collapsing-heading__arrow"></i>
                    </a>
                </div>
                <div class="collapse <?= ($collapse) ? 'in' : '' ?>" id="sale-queue-<?= $div ?>"
                     aria-expanded="<?= ($collapse) ? 'true' : 'false' ?>">
                    <div class="panel-body">
                        <?= $this->render('partial/_queueGrid', [
                            'template' => $template,
                            'dataProvider' => $dataProv,
                            'searchModel' => $searchModel,
                            'div' => $div,
                        ]); ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach;
}

?>

