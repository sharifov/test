<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use modules\abac\src\entities\AbacPolicy;
use yii\grid\ActionColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\abac\src\entities\search\AbacPolicySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $importCount int */
/* @var $abacObjectList array */
/* @var $duplicatePolicyIds array */

$this->title = 'ABAC Policies';
$this->params['breadcrumbs'][] = $this->title;
?>
<p class="abac-policy-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a(
            '<i class="fa fa-plus"></i> Create Abac Policy',
            ['create'],
            ['class' => 'btn btn-success']
        ) ?>
        <?= Html::a(
            '<i class="fa fa-list"></i> Policy list content',
            ['list-content'],
            ['class' => 'btn btn-default']
        ) ?>

        <?= Html::a('<i class="fa fa-upload"></i> Export File', ['export'], [
            'class' => 'btn btn-default',
            'data' => [
                'confirm' => 'Are you sure you want to Export all ABAC policy rules?',
            ],
        ]) ?>

        <?php if ($importCount) : ?>
            <?= Html::a(
                '<i class="fa fa-download"></i> Continue Import (' . $importCount . ')',
                ['import'],
                ['class' => 'btn btn-warning']
            ) ?>
        <?php else : ?>
            <?= Html::a(
                '<i class="fa fa-download"></i> Import File',
                ['import'],
                ['class' => 'btn btn-default']
            ) ?>
        <?php endif; ?>
    </p>

    <p>
        <i class="fa fa-info-circle"></i> <code>HashCode = md5(Object + Action + Subject + Effect)</code>


    </p>

    <?php Pjax::begin(['scrollTo' => 0]); ?>

    <?php // echo $this->render('_search', ['model' => $searchModel]);?>


    <?php if ($duplicatePolicyIds) : ?>
    <p>
        <div title="<?= implode(', ', $duplicatePolicyIds)?>">
            <i class="fa fa-warning text-warning"></i>
            Find duplicate policy item (<?php echo count($duplicatePolicyIds) ?>). Hash list:
            <?php
                $data = [];
            foreach ($duplicatePolicyIds as $hashCode) {
                $data[] = Html::a(
                    $hashCode,
                    ['/abac/abac-policy/index', 'AbacPolicySearch[ap_hash_code]' => $hashCode]
                );
            }
            ?>
            <?= implode(', ', $data)?>
        </div>
    </p>
    <?php endif; ?>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table table-bordered table-condensed table-hover'],
        'rowOptions' => static function (AbacPolicy $model) {
            if (!$model->ap_enabled) {
                return ['class' => 'danger'];
            }

//            if ($model->ap_effect === $model::EFFECT_DENY) {
//                return ['class' => 'danger'];
//            }
        },
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'ap_id',
                'options' => [
                    'style' => 'width:80px'
                ],
            ],
            //['class' => 'yii\grid\ActionColumn'],

            [
                'class' => ActionColumn::class,
                'template' => '{view} {update} {delete} {copy}',
                'buttons' => [
                    'copy' => static function ($url, AbacPolicy $model, $key) {
                        return Html::a(
                            '<span class="fa fa-copy"></span>',
                            ['copy', 'id' => $model->ap_id],
                            ['title' => 'Copy', 'target' => '_blank', 'data-pjax' => 0,
                                'data' => [
                                    'confirm' => 'Are you sure you want to copy this item?',
                                    'method' => 'post',
                                ],
                            ]
                        );
                    },
                ],
                'visibleButtons' => [
                    'copy' => static function ($model, $key, $index) {
                        return true;
                    },
                ],
            ],

            [
                'attribute' => 'ap_hash_code',
                'value' => static function (AbacPolicy $model) {
                    return $model->ap_hash_code;
                },
                'options' => [
                    'style' => 'width:100px'
                ],
            ],

            //'ap_object',
            [
                'label' => 'Status',
                'value' => static function (AbacPolicy $model) use ($abacObjectList, $duplicatePolicyIds) {
                    $exist = in_array($model->ap_object, $abacObjectList);
                    if (!$exist) {
                        return '<span class="badge badge-danger" title="Invalid object (not exists)">Error</span>';
                    }
                    $abacActionList = Yii::$app->abac->getActionListByObject($model->ap_object);
                    $actionList = @json_decode($model->ap_action_json, true);

                    if ($actionList) {
                        foreach ($actionList as $actionItem) {
                            $existAction = in_array($actionItem, $abacActionList);
                            if (!$existAction) {
                                return '<span class="badge badge-danger" title="Invalid action (' . Html::encode($actionItem) . ')">Error</span>';
                            }
                        }
                    }
                    if ($duplicatePolicyIds && in_array($model->ap_hash_code, $duplicatePolicyIds)) {
                        return '<span class="badge badge-warning" title="Duplicate (' . Html::encode($model->ap_hash_code) . ')">Duplicate</span>';
                    }

                    return '';
                },
                'format' => 'raw',
            ],


            [
                'attribute' => 'ap_object',
                'value' => static function (AbacPolicy $model) {
                    return $model->ap_object ? '<span class="badge badge-primary">' . Html::encode($model->ap_object) . '</span>' : '-';
                },
                'format' => 'raw',
            ],

            [
                'attribute' => 'ap_effect',
                'value' => static function (AbacPolicy $model) {
                    return $model->getEffectLabel();
                },
                'format' => 'raw',
                'filter' => AbacPolicy::getEffectList()
            ],

            'ap_title',



            //'ap_action',

            [
                'attribute' => 'ap_action',
                'value' => static function (AbacPolicy $model) {
                    $list = $model->getActionList();
                    $data = [];
                    if ($list) {
                        foreach ($list as $item) {
                            $data[] = Html::tag('span', $item, ['class' => 'badge badge-light']);
                        }
                    }
                    return implode(' ', $data);
                },
                'format' => 'raw',
            ],

            //'ap_rule_type',
            [
                'attribute' => 'ap_subject',
                'value' => static function (AbacPolicy $model) {
                    return '<small>' . \yii\helpers\StringHelper::truncate(str_replace('r.sub.', '', Html::encode($model->ap_subject)), 200) . '</small>';
                },
                'format' => 'raw',
            ],


            [
                'attribute' => 'ap_sort_order',
                'options' => [
                    'style' => 'width:80px'
                ],
            ],

            //'ap_subject_json',


            //'ap_action_json',
            //'ap_effect',

            'ap_enabled:boolean',
            //'ap_title',

//            'ap_created_dt',
//            'ap_updated_dt',

//            [
//                'class' => UserSelect2Column::class,
//                'attribute' => 'ap_created_user_id',
//                'relation' => 'apCreatedUser',
//                'placeholder' => 'Select User',
//            ],

            [
                'class' => UserSelect2Column::class,
                'attribute' => 'ap_updated_user_id',
                'relation' => 'apUpdatedUser',
                'placeholder' => 'Select User',
            ],

//            [
//                'class' => DateTimeColumn::class,
//                'attribute' => 'ap_created_dt',
//            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'ap_updated_dt',
            ],

            //'ap_created_user_id',
            //'ap_updated_user_id',


        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
