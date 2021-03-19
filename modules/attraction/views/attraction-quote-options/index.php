<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel modules\attraction\models\search\AttractionQuoteOptionsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Attraction Quote Options';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="attraction-quote-options-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Attraction Quote Options', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'atqo_id',
            'atqo_attraction_quote_id',
            'atqo_answered_value',
            'atqo_label',
            'atqo_is_answered',
            'atqo_answer_formatted_text',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
