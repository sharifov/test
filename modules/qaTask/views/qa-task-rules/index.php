<?php

use modules\qaTask\src\grid\columns\QaTaskObjectTypeColumn;
use common\components\grid\BooleanColumn;
use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\qaTask\src\entities\qaTaskRules\search\QaTaskRulesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Qa Task Rules';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="qa-task-rules-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'tr_id',
            'tr_key',
            [
                'class' => QaTaskObjectTypeColumn::class,
                'attribute' => 'tr_type',
            ],
            'tr_name',
            'tr_description',
            [
                'attribute' => 'tr_parameters',
                'format' => 'ntext',
                'options' => ['style' => 'width:80px'],
            ],
            [
                'class' => BooleanColumn::class,
                'attribute' => 'tr_enabled',
            ],

            [
                'class' => UserSelect2Column::class,
                'attribute' => 'tr_created_user_id',
                'relation' => 'createdUser',
                'placeholder' => 'Select User',
            ],

            [
                'class' => UserSelect2Column::class,
                'attribute' => 'tr_updated_user_id',
                'relation' => 'updatedUser',
                'placeholder' => 'Select User',
            ],

            [
                'class' => DateTimeColumn::class,
                'attribute' => 'tr_created_dt',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'tr_updated_dt',
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}{update}'
            ],
        ],
    ]) ?>

    <?php Pjax::end(); ?>

</div>
