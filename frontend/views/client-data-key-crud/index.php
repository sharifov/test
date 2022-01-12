<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\UserColumn;
use src\model\clientDataKey\entity\ClientDataKey;
use yii\grid\ActionColumn;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel src\model\clientDataKey\entity\ClientDataKeySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Client Data Keys';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-data-key-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Client Data Key', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-client-data-key']); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{errors}\n{summary}\n{items}\n{pager}",
        'columns' => [
            'cdk_id',
            [
                'attribute' => 'cdk_key',
                'value' => static function (ClientDataKey $model) {
                    return Yii::$app->formatter->asLabel($model->cdk_key);
                },
                'format' => 'raw',
            ],
            'cdk_name',
            'cdk_description',
            [
                'attribute' => 'cdk_enable',
                'format' => 'booleanByLabel',
                'filter' =>  [1 => 'Yes', 0 => 'No']
            ],
            [
                'attribute' => 'cdk_is_system',
                'format' => 'booleanByLabel',
                'filter' =>  [1 => 'Yes', 0 => 'No']
            ],
            [
                'class' => UserColumn::class,
                'relation' => 'cdkCreatedUser',
                'attribute' => 'cdk_created_user_id',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'cdk_created_dt',
                'format' => 'byUserDateTime'
            ],
            [
                'class' => ActionColumn::class,
                'template' => '{view} {update} {delete}',
                'visibleButtons' => [
                    'update' => static function (ClientDataKey $model) {
                        return !$model->cdk_is_system;
                    },
                    'delete' => static function (ClientDataKey $model) {
                        return !$model->cdk_is_system;
                    },
                ]
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
