<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\components\grid\DateTimeColumn;
use common\components\grid\BooleanColumn;
use common\components\grid\UserSelect2Column;
use yii\helpers\StringHelper;
use yii\helpers\VarDumper;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\EmailTemplateTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Email Template Types';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="email-template-type-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(['scrollTo' => 0]); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Create Email Template Type', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('<i class="fa fa-refresh"></i> Synchronization Email Template Types from Communication', ['synchronization '], ['class' => 'btn btn-warning', 'data' => [
            'confirm' => 'Are you sure you want synchronization all Email Template Types from Communication Services?',
            'method' => 'post',
        ],]) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions' => function (\common\models\EmailTemplateType $model) {
            if ($model->etp_hidden) {
                return ['class' => 'danger'];
            }
            return [];
        },
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'etp_id',
            'etp_key',
            'etp_origin_name',
            'etp_name',
            'etp_hidden:boolean',

            [
                'label' => 'Department (deprecated)',
                'attribute' => 'etp_dep_id',
                'value' => static function (\common\models\EmailTemplateType $model) {
                    return $model->etpDep ? $model->etpDep->dep_name : '-';
                },
                'filter' => false
            ],

            [
                'label' => 'Departments',
                'attribute' => 'etp_dep_id',
                'value' => static function (\common\models\EmailTemplateType $model) {
                    $valueArr = [];

                    foreach ($model->emailTemplateTypeDepartments as $item) {
                        $valueArr[] = Html::tag('div', Html::encode($item->ettdDepartment->dep_name), ['class' => 'label label-default']) ;
                    }

                    return $valueArr ? implode('<br>', $valueArr)  : '-';
                },
                'filter' => \common\models\Department::getList(),
                'format' => 'raw'
            ],

            ['class' => BooleanColumn::class, 'attribute' => 'etp_ignore_unsubscribe'],

            [
                'attribute' => 'etp_params_json',
                'value' => static function (\common\models\EmailTemplateType $model) {
                    $resultStr = '-';
                    if ($model->etp_params_json) {
                        $truncatedStr = StringHelper::truncate(
                            Html::encode(VarDumper::dumpAsString($model->etp_params_json)),
                            80,
                            '...',
                            null,
                            false
                        );

                        $detailData = VarDumper::dumpAsString($model->etp_params_json, 10, true);
                        $detailBox = '<div id="detail_' . $model->etp_id . '" style="display: none;">' . $detailData . '</div>';
                        $detailBtn = ' <i class="fas fa-eye green showDetail" style="cursor: pointer;" data-idt="' . $model->etp_id . '"></i>';

                        $resultStr = $truncatedStr . $detailBox . $detailBtn;
                    }
                    return '<small>' . $resultStr . '</small>';
                },
                'format' => 'raw'
            ],

            [
                'class' => UserSelect2Column::class,
                'attribute' => 'etp_updated_user_id',
                'relation' => 'etpUpdatedUser',
                'placeholder' => 'Select User',
            ],

            [
                'class' => DateTimeColumn::class,
                'attribute' => 'etp_updated_dt'
            ],

            /*[
                'attribute' => 'etp_updated_dt',
                'value' => static function (\common\models\EmailTemplateType $model) {
                    return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->etp_updated_dt));
                },
                'format' => 'raw',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'etp_updated_dt',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                    ],
                    'options' => [
                        'autocomplete' => 'off',
                        'placeholder' =>'Choose Date'
                    ],
                ]),
            ],*/

            [
                'class' => UserSelect2Column::class,
                'attribute' => 'etp_created_user_id',
                'relation' => 'etpCreatedUser',
                'placeholder' => 'Select User',
            ],

            [
                'class' => DateTimeColumn::class,
                'attribute' => 'etp_created_dt'
            ],

            /*[
                'attribute' => 'etp_created_dt',
                'value' => static function (\common\models\EmailTemplateType $model) {
                    return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->etp_created_dt));
                },
                'format' => 'raw',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'etp_created_dt',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                    ],
                    'options' => [
                        'autocomplete' => 'off',
                        'placeholder' =>'Choose Date'
                    ],
                ]),
            ],*/

            /*'etp_created_user_id',
            'etp_updated_user_id',
            'etp_created_dt',
            'etp_updated_dt',*/

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>

<?php
yii\bootstrap4\Modal::begin([
    'title' => 'Config params',
    'id' => 'modal',
    'size' => \yii\bootstrap4\Modal::SIZE_SMALL,
]);
yii\bootstrap4\Modal::end();

$jsCode = <<<JS
    $(document).on('click', '.showDetail', function(){
        
        let id = $(this).data('idt');
        let detailEl = $('#detail_' + id);
        let modalBodyEl = $('#modal .modal-body');
        
        modalBodyEl.html(detailEl.html()); 
        $('#modal-label').html('Email Template Params (' + id + ')');       
        $('#modal').modal('show');
        return false;
    });
JS;

$this->registerJs($jsCode, \yii\web\View::POS_READY);
?>
