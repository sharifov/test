<?php

use common\models\QuoteSegment;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\DateSensitiveSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Date Sensitive';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="date-sensitive-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php Html::a('Create Date Sensitive', ['create'], ['class' => 'btn btn-success']) ?>
    </p>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{errors}\n{summary}\n{items}\n{pager}",
        'columns' => [
            'da_key',
            'da_name',
            'da_created_user_id',
            'da_updated_user_id',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, QuoteSegment $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'qs_id' => $model->qs_id]);
                },
                'template' => '{view}',
            ],
        ],
    ]); ?>


</div>
