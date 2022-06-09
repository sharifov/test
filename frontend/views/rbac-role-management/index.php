<?php

use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\grid\ActionColumn;
use  yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider \yii\data\ArrayDataProvider */
/* @var $searchModel \yii2mod\rbac\models\search\AuthItemSearch */


$this->title = 'RBAC Role Management';
$this->params['breadcrumbs'][] = ['label' => 'RBAC', 'url' => ['/rbac-role-management']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="item-index">
    <h1><?php echo Html::encode($this->title); ?></h1>
    <?php Pjax::begin(['timeout' => 5000, 'enablePushState' => false]); ?>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'name',
                'label' => Yii::t('yii2mod.rbac', 'Name'),
            ],
            [
                'attribute' => 'description',
                'format' => 'ntext',
                'label' => Yii::t('yii2mod.rbac', 'Description'),
            ],
            [
                'class'    => ActionColumn::class,
                'template' => '{clone} {merge} {exclude}',
                'buttons'  => [
                    'clone' => function ($action, $model, $key) {
                        $url = Url::toRoute(['/rbac-role-management/clone?name=' . $model->name]);
                        return Html::a('<i class="fa fa-copy"></i>', $url, ['title' => 'Clone', 'data-pjax' => 0, 'target' => '_blank']);
                    },
                    'merge' => function ($action, $model, $key) {
                        $url = Url::toRoute(['/rbac-role-management/merge?name=' . $model->name]);
                        return Html::a('<i class="fa fa-toggle-down"></i>', $url, ['title' => 'Merge', 'data-pjax' => 0, 'target' => '_blank']);
                    },
                    'exclude' => function ($action, $model, $key) {
                        $url = Url::toRoute(['/rbac-role-management/exclude?name=' . $model->name]);
                        return Html::a('<i class="fa fa-stop"></i>', $url, ['title' => 'Exclude', 'data-pjax' => 0, 'target' => '_blank']);
                    },
                ]
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>
</div>