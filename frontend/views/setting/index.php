<?php

use common\models\SettingCategory;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\VarDumper;
use yii\widgets\Pjax;
use common\components\grid\DateTimeColumn;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\SettingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Site Settings';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="setting-index">

    <h1><i class="fa fa-cogs"></i> <?= Html::encode($this->title) ?></h1>

     <?= Html::a('<i class="fa fa-close"></i> Clear Cache', ['clean'], [
        'class' => 'btn btn-danger',
        'data' => [
            'confirm' => 'Are you sure you want to clear Site Settings cache data?'
        ],
     ]) ?>

    <p>
        <?php //= Html::a('Create Setting', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['scrollTo' => 0]); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 's_id',
                'options' => ['style' => 'width: 100px']
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update}'
            ],
            [
                'attribute' => 's_name',
                'value' => static function (\common\models\Setting $model) {
                    return '<b title="' . Html::encode($model->s_description) . '" data-toggle="tooltip">' . ($model->s_description ? '<i class="fa fa-info-circle info"></i> ' : '') . Html::encode($model->s_name) . '</b>';
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 's_category_id',
                'value' => static function (\common\models\Setting $model) {
                    return $model->sCategory ? $model->sCategory->sc_name : '-';
                },
                'filter' => SettingCategory::getList()
            ],
            [
                'attribute' => 's_key',
                'value' => static function (\common\models\Setting $model) {
                    return '<span class="label label-default">' . Html::encode($model->s_key) . '</span>';
                },
                'format' => 'raw',
            ],

            //'s_type',



            [
                'attribute' => 's_value',
                'value' => static function (\common\models\Setting $model) {

                    $val = Html::encode($model->s_value);

                    if ($model->s_type == \common\models\Setting::TYPE_BOOL) {
                        $val = $model->s_value ? '<span class="label label-success">true</span>' : '<span class="label label-danger">false</span>';
                    }

                    if ($model->s_type == \common\models\Setting::TYPE_ARRAY) {
                        $val = '<pre><small>' . ($model->s_value ? VarDumper::dumpAsString(@json_decode($model->s_value, true), 10, false) : '-') . '</small></pre>';
                    }

                    return $val;
                },
                'format' => 'raw',
                //'filter' => false
            ],

            [
                'attribute' => 's_type',
                'value' => static function (\common\models\Setting $model) {
                    return $model->s_type;
                },
                //'format' => 'raw',
                'filter' => \common\models\Setting::TYPE_LIST
            ],

            /*[
                'attribute' => 's_updated_user_id',
                'value' => static function (\common\models\Setting $model) {
                    return ($model->sUpdatedUser ? '<i class="fa fa-user"></i> ' .Html::encode($model->sUpdatedUser->username) : $model->s_updated_user_id);
                },
                'format' => 'raw',
                'filter' => \common\models\Employee::getList()
            ],*/

            [
                'class' => \common\components\grid\UserSelect2Column::class,
                'attribute' => 's_updated_user_id',
                'relation' => 'sUpdatedUser',
                'placeholder' => 'Select User',
            ],

            [
                'class' => DateTimeColumn::class,
                'attribute' => 's_updated_dt'
            ],

            /*[
                'attribute' => 's_updated_dt',
                'value' => static function (\common\models\Setting $model) {
                    return $model->s_updated_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->s_updated_dt)) : $model->s_updated_dt;
                },
                'format' => 'raw'
            ],*/


        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
