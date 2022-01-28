<?php

use modules\featureFlag\src\entities\FeatureFlag;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\helpers\VarDumper;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\featureFlag\src\entities\search\FeatureFlagSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Feature Flags';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="feature-flag-index">

    <h1><i class="fa fa-flag"></i> <?= Html::encode($this->title) ?></h1>

    <?= Html::a('<i class="fa fa-close"></i> Clear Cache', ['clear-cache'], [
        'class' => 'btn btn-danger',
        'data' => [
            'confirm' => 'Are you sure you want to clear Feature Flags cache data?'
        ],
    ]) ?>

    <?= Html::a('<i class="fa fa-plus"></i> Create', ['create'], [
        'class' => 'btn btn-primary',
    ]) ?>

    <p>
        <?php //= Html::a('Create Setting', ['create'], ['class' => 'btn btn-success'])?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-feature-flag']); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'ff_id',
                'options' => ['style' => 'width: 100px']
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update}'
            ],
            [
                'attribute' => 'ff_name',
                'value' => static function (FeatureFlag $model) {
                    return '<b title="' . Html::encode($model->ff_description) . '" data-toggle="tooltip">' .
                        ($model->ff_description ? '<i class="fa fa-info-circle info"></i> ' : '') .
                        Html::encode($model->ff_name) . '</b>';
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'ff_category',
                'value' => static function (FeatureFlag $model) {
                    return $model->ff_category ? $model->ff_category : '-';
                },
                //'filter' => SettingCategory::getList()
            ],
            [
                'attribute' => 'ff_key',
                'value' => static function (FeatureFlag $model) {
                    return '<span class="label label-default">' . Html::encode($model->ff_key) . '</span>';
                },
                'format' => 'raw',
            ],

            [
                'attribute' => 'ff_enable_type',
                'value' => static function (FeatureFlag $model) {
                    return $model->getEnableTypeLabel();
                },
                'format' => 'raw',
                'filter' => FeatureFlag::getEnableTypeList()
            ],

            [
                'attribute' => 'ff_value',
                'value' => static function (FeatureFlag $model) {
                    $val = Html::encode($model->ff_value);

                    if ($model->ff_type === FeatureFlag::TYPE_BOOL) {
                        $val = $model->ff_value ? '<span class="label label-success">true</span>' :
                            '<span class="label label-danger">false</span>';
                    }

                    if ($model->ff_type === FeatureFlag::TYPE_ARRAY) {
                        $val = '<pre><small>' . ($model->ff_value ?
                                VarDumper::dumpAsString(@json_decode($model->ff_value, true), 10, false) : '-') .
                            '</small></pre>';
                    }

                    return $val;
                },
                'format' => 'raw',
                //'filter' => false
            ],

            [
                'attribute' => 'ff_type',
                'value' => static function (FeatureFlag $model) {
                    return $model->ff_type;
                },
                //'format' => 'raw',
                'filter' => FeatureFlag::TYPE_LIST
            ],

            //'ff_category',
            //'ff_description',

            //'ff_attributes',
            'ff_condition',

//            [
//                'attribute' => 'ff_updated_user_id',
////                'value' => static function (\common\models\Setting $model) {
////                    return ($model->sUpdatedUser ? '<i class="fa fa-user"></i> ' .Html::encode($model->sUpdatedUser->username) : $model->s_updated_user_id);
////                },
////                'format' => 'raw',
////                'filter' => \common\models\Employee::getList()
//            ],

//            /*[
//                'attribute' => 's_updated_user_id',
//                'value' => static function (\common\models\Setting $model) {
//                    return ($model->sUpdatedUser ? '<i class="fa fa-user"></i> ' .Html::encode($model->sUpdatedUser->username) : $model->s_updated_user_id);
//                },
//                'format' => 'raw',
//                'filter' => \common\models\Employee::getList()
//            ],*/

//            [
//                'class' => \common\components\grid\UserSelect2Column::class,
//                'attribute' => 'ff_updated_user_id',
//                'relation' => 'sUpdatedUser',
//                'placeholder' => 'Select User',
//            ],

//            [
//                'class' => DateTimeColumn::class,
//                'attribute' => 's_updated_dt'
//            ],

            [
                'attribute' => 'ff_updated_dt',
                'value' => static function (FeatureFlag $model) {
                    return $model->ff_updated_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->ff_updated_dt)) : $model->ff_updated_dt;
                },
                'format' => 'raw'
            ],


        ],
    ]); ?>

    <?php Pjax::end(); ?>


</div>
