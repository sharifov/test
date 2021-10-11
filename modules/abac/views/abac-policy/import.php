<?php

use common\components\grid\DateTimeColumn;
use modules\abac\src\entities\AbacPolicy;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\VarDumper;
use yii\web\UploadedFile;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model UploadedFile */
/* @var $header array */
/* @var $data array */
/* @var $filePath string */
/* @var $dataProvider ArrayDataProvider */

$this->title = 'ABAC policy Import';
$this->params['breadcrumbs'][] = ['label' => 'Abac Policies', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="abac-policy-import">
    <h1><i class="fa fa-download"></i> <?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a('<i class="fa fa-check"></i> Cancel Import', ['import-cancel'], ['class' => 'btn btn-danger']) ?>
    </p>

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

    <?= $form->field($model, 'importFile')->fileInput(['accept' => ".json"]) ?>
    <?php echo Html::submitButton('<i class="fa fa-upload"></i> Upload File', ['class' => 'btn btn-submit btn-primary']); ?>
    <?php ActiveForm::end() ?>

    <?php if ($header) : ?>
        <h2>File: <?php echo Html::encode($filePath) ?></h2>
        <div class="row">
            <div class="col-md-6">
                <?= DetailView::widget([
                    'model' => $header,
                    'attributes' => [
                        'app_name',
                        'app_ver',
                        'env',
                        'schema_ver',
                        'username',
                        'datetime',
                    ],
                ]) ?>
            </div>
        </div>

    <div class="row">
        <div class="col-md-12">
        <?php Pjax::begin(); ?>
        <?php // echo $this->render('_search', ['model' => $searchModel]);?>

        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            //'filterModel' => $searchModel,
            'tableOptions' => ['class' => 'table table-bordered table-condensed table-hover'],
            'rowOptions' => static function ($model) {
//                if (!$model['enabled']) {
//                    return ['class' => 'danger'];
//                }

//            if ($model->ap_effect === $model::EFFECT_DENY) {
//                return ['class' => 'danger'];
//            }
            },
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

//                [
//                    'attribute' => 'id',
//                    'options' => [
//                        'style' => 'width:80px'
//                    ],
//                ],


//                ['class' => 'yii\grid\ActionColumn'],
//
//                //'ap_object',
//
//                [
//                    'attribute' => 'ap_object',
//                    'value' => static function (AbacPolicy $model) {
//                        return $model->ap_object ? '<span class="badge badge-primary">' . Html::encode($model->ap_object) . '</span>' : '-';
//                    },
//                    'format' => 'raw',
//                ],
//


                [
                    'attribute' => 'object',
                    'value' => static function ($model) {
                        return $model['object'] ? '<span class="badge badge-primary">' . Html::encode($model['object']) . '</span>' : '-';
                    },
                    'format' => 'raw',
                ],

                'subject',
                'action',
                //'effect',
                [
                    'attribute' => 'effect',
                    'value' => static function ($model) {
                        return AbacPolicy::EFFECT_LIST[$model['effect'] ?? ''] ?? '-';
                    },
                    'format' => 'raw',
                    //'filter' => AbacPolicy::getEffectList()
                ],

                [
                    'attribute' => 'sort_order',
                    'options' => [
                        'style' => 'width:80px'
                    ],
                ],

                'enabled:boolean',

                [
                    'class' => DateTimeColumn::class,
                    'attribute' => 'created_dt',
                ],
                [
                    'class' => DateTimeColumn::class,
                    'attribute' => 'updated_dt',
                ],
            ],
        ]); ?>

        <?php echo Html::submitButton('<i class="fa fa-download"></i> Import selected policy', ['class' => 'btn btn-submit btn-success']); ?>
        <?php ActiveForm::end() ?>

        <?php Pjax::end(); ?>
        </div>
    </div>


    <?php endif; ?>
</div>
