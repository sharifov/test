<?php

use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this \yii\web\View */
/* @var $gridViewColumns array */
/* @var $dataProvider \yii\data\ArrayDataProvider */
/* @var $searchModel \yii2mod\rbac\models\search\AssignmentSearch */

$this->title = Yii::t('yii2mod.rbac', 'Assignments');
$this->params['breadcrumbs'][] = ['label' => 'RBAC', 'url' => ['/rbac']];
$this->params['breadcrumbs'][] = $this->title;
$this->render('/layouts/_sidebar');
?>
<div class="assignment-index">

    <h1><?php echo Html::encode($this->title); ?></h1>

    <?php Pjax::begin(['timeout' => 5000]); ?>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => ArrayHelper::merge($gridViewColumns, [
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => function ($url,\common\models\Employee $model) {
                        return $model->isActive() ? Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
                            'title' => Yii::t('app', 'lead-view')
                        ]) : '';
                    },
                ]
            ],
        ]),
        'rowOptions' => function (\common\models\Employee $model) {
            if (!$model->isActive()) {
                return ['class' => 'danger'];
            }
        },
    ]); ?>

    <?php Pjax::end(); ?>
</div>
