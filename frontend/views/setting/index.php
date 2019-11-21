<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\SettingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Site Settings';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="setting-index">

    <h1><i class="fa fa-cogs"></i> <?= Html::encode($this->title) ?></h1>

    <p>
        <?//= Html::a('Create Setting', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            's_id',
            's_key',
            's_name',
            //'s_type',

            [
                'attribute' => 's_type',
                'value' => static function (\common\models\Setting $model) {
                    return $model->s_type;
                },
                //'format' => 'raw',
                'filter' => \common\models\Setting::TYPE_LIST
            ],

            [
                'attribute' => 's_value',
                'value' => static function (\common\models\Setting $model) {

                    $val = Html::encode($model->s_value);

                    if($model->s_type == \common\models\Setting::TYPE_BOOL) {
                        $val = $model->s_value ? '<span class="label label-success">true</span>' : '<span class="label label-danger">false</span>';
                    }

                    if($model->s_type == \common\models\Setting::TYPE_ARRAY) {
                        $val = '<pre>' . ($model->s_value ? print_r(@json_decode($model->s_value, true), true) : '-') .'</pre>';
                    }

                    return $val;
                },
                'format' => 'raw',
                //'filter' => false
            ],

            [
                'attribute' => 's_updated_user_id',
                'value' => static function (\common\models\Setting $model) {
                    return ($model->sUpdatedUser ? '<i class="fa fa-user"></i> ' .Html::encode($model->sUpdatedUser->username) : $model->s_updated_user_id);
                },
                'format' => 'raw',
                'filter' => \common\models\Employee::getList()
            ],

            [
                'attribute' => 's_updated_dt',
                'value' => static function (\common\models\Setting $model) {
                    return $model->s_updated_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->s_updated_dt)) : $model->s_updated_dt;
                },
                'format' => 'raw'
            ],

            [
                    'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update}'
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
