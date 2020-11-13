<?php

use yii\grid\ActionColumn;
use common\components\grid\DateTimeColumn;
use sales\model\call\entity\callCommand\PhoneLineCommand;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel sales\model\call\entity\callCommand\search\PhoneLineCommandSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Phone Line Commands';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="phone-line-command-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-plus"></i>  Create Phone Line Command', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'plc_id',
            'plc_line_id',
            'plc_ccom_id',
            'plc_sort_order',
            //'plc_created_user_id',
            [
                'attribute' => 'plc_created_user_id',
                'filter' => \sales\widgets\UserSelect2Widget::widget([
                    'model' => $searchModel,
                    'attribute' => 'plc_created_user_id'
                ]),
                'format' => 'username',
                'options' => [
                    'width' => '150px'
                ]
            ],

            [
                'class' => DateTimeColumn::class,
                'attribute' => 'plc_created_dt'
            ],

            /*[
                'attribute' => 'plc_created_dt',
                'value' => static function (PhoneLineCommand $model) {
                    return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->plc_created_dt));
                },
                'format' => 'raw',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'plc_created_dt',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                        'clearBtn' => true,
                    ],
                    'options' => [
                        'autocomplete' => 'off',
                        'placeholder' =>'Choose Date'
                    ],
                    'clientEvents' => [
                        'clearDate' => 'function (e) {$(e.target).find("input").change();}',
                    ],
                ]),
            ],*/
            ['class' => ActionColumn::class],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
