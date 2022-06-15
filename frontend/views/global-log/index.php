<?php

use src\auth\Auth;
use src\services\cleaner\form\DbCleanerParamsForm;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
use common\components\grid\DateTimeColumn;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\GlobalLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var DbCleanerParamsForm $modelCleaner */

$this->title = 'Global Logs';
$this->params['breadcrumbs'][] = $this->title;
$view = $this;
$pjaxListId = 'pjax-global-log';
?>
<div class="global-log-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php //    = Html::a('Create Global Log', ['create'], ['class' => 'btn btn-success'])?>
    </p>

    <?php if (Auth::can('global/clean/table')) : ?>
        <?php echo $this->render('../clean/_clean_table_form', [
            'modelCleaner' => $modelCleaner,
            'pjaxIdForReload' => $pjaxListId,
        ]); ?>
    <?php endif ?>

    <?php Pjax::begin(['id' => $pjaxListId, 'timeout' => 8000, 'scrollTo' => 0]); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <?php  echo $this->render('_pagination', ['model' => $searchModel]);?>

    <?= $searchModel->filterCount ? 'Find <b>' . $searchModel->filterCount . '</b> items' : null ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'filterUrl' => Url::to(['global-log/index']),
        'layout' => "{items}",
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'gl_id',
                'enableSorting' => false,
            ],
            // 'gl_app_id',
            [
                'attribute' => 'gl_app_id',
                'value' => static function (\common\models\GlobalLog $model) {
                    return $model->getAppName() ?: $model->gl_app_id;
                },
                'filter' => \common\models\GlobalLog::getAppList()
            ],
            'gl_app_user_id',
            // 'gl_model',
            [
                'attribute' => 'gl_model',
                'value' => static function (\common\models\GlobalLog $model) {
                    return $model->getModelName() ?: $model->gl_model;
                },
                'filter' => \common\models\GlobalLog::getModelList()
            ],
            'gl_obj_id',
            [
                'attribute' => 'gl_formatted_attr',
                'value' => static function (\common\models\GlobalLog $model) use ($view) {
                    if ($model->gl_formatted_attr) {
                        return $view->render('partial/_formatted_attributes', [
                            'model' => $model
                        ]);
                    } else {
                        return 'Data log not formatted yet';
                    }
                },
                'enableSorting' => false,
                'format' => 'raw'
            ],

            [
                'attribute' => 'gl_action_type',
                'value' => static function (\common\models\GlobalLog $model) {
                    return $model->getActionTypeName() ?: $model->gl_action_type;
                },
                'filter' => \common\models\GlobalLog::getActionTypeList()
            ],

            [
                'class' => DateTimeColumn::class,
                'attribute' => 'gl_created_at'
            ],

            [

                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}'
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
