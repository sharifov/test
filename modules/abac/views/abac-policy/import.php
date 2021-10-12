<?php

use common\components\grid\DateTimeColumn;
use modules\abac\src\entities\AbacPolicy;
use modules\abac\src\forms\AbacPolicyImportForm;
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
/* @var $headerLocal array */
/* @var $data array */
/* @var $abacObjectList array */
/* @var $filePath string */
/* @var $dataProvider ArrayDataProvider */
/* @var $isCache bool */

$this->title = 'ABAC policy Import';
$this->params['breadcrumbs'][] = ['label' => 'Abac Policies', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="abac-policy-import">
    <h1><i class="fa fa-download"></i> <?= Html::encode($this->title) ?></h1>
    <p>
        <?php if ($isCache) : ?>
            <?= Html::a('<i class="fa fa-remove"></i> Cancel Import (Reset Cache Data)', ['import-cancel'], ['class' => 'btn btn-danger']) ?>
        <?php endif; ?>
    </p>

    <?php if (!$isCache) : ?>
        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

        <?= $form->field($model, 'importFile')->fileInput(['accept' => ".json"]) ?>
        <?php echo Html::submitButton('<i class="fa fa-upload"></i> Upload File', ['class' => 'btn btn-submit btn-primary']); ?>
        <?php ActiveForm::end() ?>
        <?php if ($header) : ?>
            <h2>File: <?php echo Html::encode($filePath) ?></h2>
        <?php endif; ?>
    <?php endif; ?>

    <?php if ($header) : ?>
        <div class="row">
            <div class="col-md-6">
                <h4>Import Data Info</h4>
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
            <div class="col-md-6">
                <h4>Local Data Info</h4>
                <?= DetailView::widget([
                    'model' => $headerLocal,
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

        <h3>Import policy data</h3>
    <div class="row">
        <div class="col-md-12">
        <?php Pjax::begin(); ?>
        <?php // echo $this->render('_search', ['model' => $searchModel]);?>

        <?php $form = ActiveForm::begin(['action' => 'import-ids']) ?>
            <?php /*
            <div class="row">
                <div class="col-md-3">
                    <?= $form->field($model, 'import_type_id')->dropDownList(AbacPolicyImportForm::IMPORT_TYPE_LIST) ?>
                </div>
            </div>*/
            ?>

            <?php echo Html::submitButton('<i class="fa fa-check"></i> Submit selected policies', ['class' => 'btn btn-submit btn-success', 'title' => 'Import selected policy']); ?>

            <?= $form->errorSummary($model) ?>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            //'filterModel' => $searchModel,
            'tableOptions' => ['class' => 'table table-bordered table-condensed table-hover'],
            'rowOptions' => static function ($data) {
                if ($data['action_id'] == AbacPolicyImportForm::ACT_ERROR) {
                    return ['class' => 'danger'];
                } elseif ($data['action_id'] == AbacPolicyImportForm::ACT_CREATE) {
                    return ['class' => 'text-success'];
                }
                return [];
            },
            'columns' => [
                [
                    'class' => 'yii\grid\CheckboxColumn',
//                    'name' => 'id',

                    'name' => 'AbacPolicyImportForm[ids]',
                    'checkboxOptions' => static function ($data) {
                        if ((int) $data['action_id'] === AbacPolicyImportForm::ACT_CREATE) {
                            return ['value' => $data['id']];
                        }
                        if ((int) $data['action_id'] === AbacPolicyImportForm::ACT_ERROR) {
                            return ['style' => ['display' => 'none'], 'disabled' => true];
                        }
                        return ['disabled' => true];
                    },

                ],
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
                [
                    'attribute' => 'action_id',
                    'value' => static function ($data) {
                        return AbacPolicyImportForm::getActionTitle($data['action_id'] ?? '');
                    },
                    'format' => 'raw',
                ],


                [
                    'attribute' => 'object',
                    'value' => static function ($data) use ($abacObjectList) {
                        $existObject = in_array($data['object'], $abacObjectList);

                        if (!$existObject) {
                            return '<i class="fa fa-warning" title="Invalid object (Not found in system)"></i> <span class="badge badge-danger"><s>' . Html::encode($data['object']) . '</s></span>';
                        }


                        return $data['object'] ? '<span class="badge badge-light"><b>' . Html::encode($data['object']) . '</b></span>' : '-';
                    },
                    'format' => 'raw',
                ],

                //'action',
                [
                    'attribute' => 'action',
                    'value' => static function ($data) {
                        $list = @json_decode($data['action_json'], true);
                        $listData = [];

                        if ($list) {
                            foreach ($list as $item) {
                                $existObject = in_array($item, $data['abac_action_list'] ?? []);
                                if (!$existObject) {
                                    $listData[] = '<i class="fa fa-warning" title="Invalid action (Not found in system)"></i> <span class="badge badge-danger"><s>' . Html::encode($item) . '</s></span>';
                                } else {
                                    $listData[] = '<span class="badge badge-light">' . Html::encode($item) . '</span>';
                                }
                            }
                        }

                        return implode(' ', $listData);
                    },
                    'format' => 'raw',
                    //'filter' => AbacPolicy::getEffectList()
                ],

                //'effect',
                [
                    'attribute' => 'effect',
                    'value' => static function ($data) {
                        return AbacPolicy::EFFECT_LIST[$data['effect'] ?? ''] ?? '-';
                    },
                    'format' => 'raw',
                    //'filter' => AbacPolicy::getEffectList()
                ],

                [
                    'attribute' => 'subject',
                    'value' => static function ($data) {
                        return '<small>' . str_replace('r.sub.', '', Html::encode($data['subject'])) . '</small>';
                    },
                    'format' => 'raw',
                    //'filter' => AbacPolicy::getEffectList()
                ],
                //'subject',

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

        <?php echo Html::submitButton('<i class="fa fa-check"></i> Submit selected policies', ['class' => 'btn btn-submit btn-success']); ?>
        <?php ActiveForm::end() ?>

        <?php Pjax::end(); ?>
        </div>
    </div>


    <?php endif; ?>
</div>
