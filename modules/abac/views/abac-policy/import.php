<?php

use common\components\grid\DateTimeColumn;
use modules\abac\src\entities\AbacPolicy;
use modules\abac\src\entities\search\AbacPolicyImportSearch;
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
/* @var $searchModel AbacPolicyImportSearch */


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
            <div class="col-md-4">
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
            <div class="col-md-4">
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
            <div class="col-md-4">
                <h4>Local Stats</h4>
                <table class="table table-striped table-bordered">
                    <tbody>
                        <tr>
                            <th>Object list count</th>
                            <td><?php echo count(Yii::$app->abac->getObjectList()) ?></td>
                        </tr>
                        <tr>
                            <th>All Policies count</th>
                            <td><?php echo Yii::$app->abac->getPolicyListCount() ?></td>
                        </tr>
                        <tr>
                            <th>Enabled Policies count</th>
                            <td><?php echo Yii::$app->abac->getPolicyListCount(true) ?></td>
                        </tr>
                    </tbody>
                </table>
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
            <?= $form->field($model, 'import_ids')->hiddenInput(['id' => 'import_ids'])->label(false) ?>


            <?php echo Html::submitButton('<i class="fa fa-check"></i> Submit selected policies', ['class' => 'btn btn-submit btn-success btn-submit', 'title' => 'Import selected policy']); ?>

            <?= $form->errorSummary($model) ?>

        <?php ActiveForm::end() ?>

        <?= GridView::widget([
                'id' => 'import-grid-view',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
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
    //                  'name' => 'id',
                        'cssClass' => 'multiple-checkbox',

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


                    [
                        'attribute' => 'action_id',
                        'value' => static function ($data) {
                            return AbacPolicyImportForm::getActionTitle($data['action_id'] ?? '');
                        },
                        'format' => 'raw',
                        'filter' => AbacPolicyImportForm::ACT_LIST
                    ],

                    [
                        'attribute' => 'id',
                        'options' => [
                            'style' => 'width:60px'
                        ],
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

                    [
                        'attribute' => 'effect',
                        'value' => static function ($data) {
                            return AbacPolicy::EFFECT_LIST[$data['effect'] ?? ''] ?? '-';
                        },
                        'format' => 'raw',
                        'filter' => AbacPolicy::getEffectList()
                    ],

                    [
                        'attribute' => 'subject',
                        'value' => static function ($data) {
                            return '<small>' . str_replace('r.sub.', '', Html::encode($data['subject'])) . '</small>';
                        },
                        'format' => 'raw',
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

        <?php Pjax::end(); ?>
        </div>
    </div>

    <?php endif; ?>
</div>

<?php
$js = <<<JS
    $('body').on('change', '.select-on-check-all', function(e) {
        let checked = $('#import-grid-view').yiiGridView('getSelectedRows');
        let data = [];
        $.each( checked, function( key, value ) {
            let val = $('#import-grid-view').find('tr[data-key="' + value + '"]').find('.multiple-checkbox').val()
            data[key] = parseInt(val);
        });
        let strVal = '';
        if (data.length) {
            strVal = JSON.stringify(data);
        }
        updateSubmitTitle(data.length);
       $('#import_ids').val(strVal);
    });

    function updateSubmitTitle(count)
    {
        let text = '<i class="fa fa-check"></i> Submit selected policies';
        if (count > 0) {
            text =  '<i class="fa fa-check-square"></i> Submit selected policies ( ' + count + ' )';
        }
        $('.btn-submit').html(text);
    }
JS;

$this->registerJs($js, \yii\web\View::POS_READY);
?>