<?php
use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\SaleSearch */
/* @var $dataProvider yii\data\ArrayDataProvider */

$this->title = 'Search Sale';
$this->params['breadcrumbs'][] = $this->title;


?>
<style>
.dropdown-menu {
	z-index: 1010;
}
</style>
<div class="sale-search">

	<h1><?= Html::encode($this->title) ?></h1>

    <?php Pjax::begin(['id' => 'sale-pjax-list', 'timeout' => 15000, 'enablePushState' => true]); ?>
    <?php
        echo $this->render('_search', [
            'model' => $searchModel
        ]);

        echo GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'sale_id',
                'value' => function ($model) {
                    return $model['sale_id'];
                },
                'format' => 'raw',
                'options' => [
                    'style' => 'width:80px'
                ],
                'contentOptions' => [
                    'class' => 'text-center'
                ]
            ],


            /*[
                'attribute' => 'created',
                'value' => function (\common\models\Lead $model) {
                    return $model->created ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->created)) : '-';
                },
                'format' => 'raw'
            ],
            // 'created:date',

            [
                'attribute' => 'updated',
                'value' => function(\common\models\Lead $model) {
                    $str = '-';
                    if($model->updated) {
                        $str = '<b>' . Yii::$app->formatter->asRelativeTime(strtotime($model->updated)) . '</b>';
                        $str .= '<br><i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->updated));
                    }
                    return $str;
                },
                'format' => 'raw',
                'contentOptions' => [
                    'class' => 'text-center'
                ],
            ],
         */


            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}'
            ]
        ],

        ]);
        // }

        ?>
    <?php Pjax::end(); ?>
<?php

/*$js = <<<JS

    $(document).on('pjax:start', function() {
        $("#modalUpdate .close").click();
    });

    $(document).on('pjax:end', function() {
         $('[data-toggle="tooltip"]').tooltip();
    });


   $('[data-toggle="tooltip"]').tooltip();


JS;
$this->registerJs($js, \yii\web\View::POS_READY);*/
?>
</div>