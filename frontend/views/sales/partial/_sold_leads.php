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
            [
                'attribute' => 'l_status_dt',
                'class' => DateColumn::class,
                'label' => 'Sold Date',
            ],
            [
                'attribute' => 'created',
                'class' => DateColumn::class,
            ],
        ],
    ]) ?>

<?php Pjax::end();
