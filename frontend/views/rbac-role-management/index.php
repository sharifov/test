<?php

use src\rbac\services\RbacQueryService;
use yii\db\Query;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\grid\ActionColumn;
use  yii\helpers\Url;
use yii2mod\rbac\models\search\AuthItemSearch;

/* @var $this yii\web\View */
/* @var $dataProvider \yii\data\ArrayDataProvider */
/* @var $searchModel \yii2mod\rbac\models\search\AuthItemSearch */


$this->title = 'RBAC Role Management';
$this->params['breadcrumbs'][] = ['label' => 'RBAC', 'url' => ['/rbac-role-management']];
$this->params['breadcrumbs'][] = $this->title;
$roleList  = RbacQueryService::getRolesList();

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
                'attribute' => 'permissionsCount',
                'value'     => function (yii\rbac\Role $model) use ($roleList) {
                    return (new Query())
                        ->select('a.child')
                        ->from(['a' => 'auth_item_child'])
                        ->where(['a.parent' => $model->name])
                        ->andWhere(['NOT IN', 'a.child', $roleList])
                        ->count();
                }
            ],
            [
                'class'    => ActionColumn::class,
                'template' => '{rewrite} {merge} {exclude} {view}',
                'buttons'  => [
                    'rewrite' => function ($action, $model, $key) {
                        $url = Url::toRoute(['/rbac-role-management/rewrite?name=' . $model->name]);
                        return Html::a('<i class="fa fa-copy"></i>', $url, ['title' => 'Rewrite To', 'data-pjax' => 0, 'target' => '_blank']);
                    },
                    'merge' => function ($action, $model, $key) {
                        $url = Url::toRoute(['/rbac-role-management/merge?name=' . $model->name]);
                        return Html::a('<i class="fa fa-mercury"></i>', $url, ['title' => 'Merge', 'data-pjax' => 0, 'target' => '_blank']);
                    },
                    'exclude' => function ($action, $model, $key) {
                        $url = Url::toRoute(['/rbac-role-management/exclude?name=' . $model->name]);
                        return Html::a('<i class="fa fa-bomb"></i>', $url, ['title' => 'Exclude', 'data-pjax' => 0, 'target' => '_blank']);
                    },
                    'view' => function ($action, $model, $key) {
                        $url = Url::toRoute(['/rbac/role/view?id=' . $model->name]);
                        return Html::a('<i class="fa fa-eye"></i>', $url, ['title' => 'View', 'data-pjax' => 0, 'target' => '_blank']);
                    },
                ]
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>
</div>