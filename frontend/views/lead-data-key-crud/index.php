<?php

use yii\grid\ActionColumn;
use common\components\grid\DateTimeColumn;
use common\components\grid\UserColumn;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var yii\web\View $this */
/* @var sales\model\leadDataKey\entity\LeadDataKeySearch $searchModel */
/* @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Lead Data Keys';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-data-key-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Lead Data Key', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-lead-data-key']); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{errors}\n{summary}\n{items}\n{pager}",
        'columns' => [
            'ldk_id',
            'ldk_key',
            'ldk_name',
            'ldk_enable:booleanByLabel',
            [
                'class' => UserColumn::class,
                'relation' => 'ldkCreatedUser',
                'attribute' => 'ldk_created_user_id',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'ldk_created_dt',
                'format' => 'byUserDateTime'
            ],
            ['class' => ActionColumn::class],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
