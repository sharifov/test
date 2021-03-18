<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\attraction\models\AttractionQuoteOptions */

$this->title = $model->atqo_id;
$this->params['breadcrumbs'][] = ['label' => 'Attraction Quote Options', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="attraction-quote-options-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->atqo_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->atqo_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'atqo_id',
            'atqo_attraction_quote_id',
            'atqo_answered_value',
            'atqo_label',
            'atqo_is_answered',
            'atqo_answer_formatted_text',
        ],
    ]) ?>

</div>
