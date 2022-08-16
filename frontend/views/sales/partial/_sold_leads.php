<?php

use common\components\grid\DateColumn;
use src\model\user\entity\sales\SalesSearch;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var yii\web\View $this */
/* @var SalesSearch $searchModel */
/* @var yii\data\ActiveDataProvider $dataProvider */
?>

<?php Pjax::begin(['id' => 'pjax-sold-leads', 'timeout' => 5000, 'enablePushState' => false]); ?>
<?php
$l_status_dt_column = [
    'attribute' => 'l_status_dt',
    'class' => DateColumn::class,
    'label' => 'Sold Date',
];

$created = [
    'attribute' => 'created',
    'class' => DateColumn::class,
];

/** @fflag FFlag::FF_KEY_CONVERSION_BY_TIMEZONE, Conversion Filter by Timezone */
if (\Yii::$app->featureFlag->isEnable(\modules\featureFlag\FFlag::FF_KEY_CONVERSION_BY_TIMEZONE)) {
    $l_status_dt_column = [
        'attribute' => 'l_status_dt',
        'class' => DateColumn::class,
        'label' => 'Sold Date',
        'format' => 'raw',
        'value' => function ($model) use ($searchModel) {
            $l_status_dt = ((new \DateTimeImmutable($model['l_status_dt'], new \DateTimeZone($searchModel->getTimezone())))->format('d-M-Y'));
            return Html::tag('i', '', ['class' => 'fa fa-calendar']) . ' ' . $l_status_dt;
        }
    ];
    $created = [
        'attribute' => 'created',
        'class' => DateColumn::class,
        'format' => 'raw',
        'value' => function ($model) use ($searchModel) {
            $created = ((new \DateTimeImmutable($model['created'], new \DateTimeZone($searchModel->getTimezone())))->format('d-M-Y'));
            return Html::tag('i', '', ['class' => 'fa fa-calendar']) . ' ' . $created;
        }
    ];
}
?>
<?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{errors}\n{items}\n{pager}",
        'columns' => [
            [
                'attribute' => 'id',
                'format' => 'raw',
                'label' => 'Lead',
                'value' => static function ($data) {
                    return Html::tag('i', '', ['class' => 'fa fa-arrow-right'])
                        . ' '
                        . Html::a(
                            'lead: ' . $data['id'],
                            ['/lead/view', 'gid' => $data['gid']],
                            ['target' => '_blank', 'data-pjax' => 0]
                        );
                }
            ],
            [
                'attribute' => 'final_profit',
                'format' => 'raw',
                'label' => 'Gross Profit',
                'value' => static function ($data) {
                    return Html::tag('i', '', ['class' => 'fa fa-money']) . ' ' . $data['gross_profit'];
                },
                'contentOptions' => [
                    'style' => 'width:420px'
                ],
            ],
            [
                'attribute' => 'share',
                'value' => static function ($data) {
                     return ($data['share'] * 100) . ' %' ;
                },
                'contentOptions' => [
                    'class' => 'text-center'
                ],
            ],
            $l_status_dt_column,
            $created,
        ],
    ]) ?>

<?php Pjax::end();
