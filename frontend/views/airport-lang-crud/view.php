<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\airportLang\entity\AirportLang */

$this->title = $model->ail_iata;
$this->params['breadcrumbs'][] = ['label' => 'Airport Langs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="airport-lang-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'ail_iata' => $model->ail_iata, 'ail_lang' => $model->ail_lang], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'ail_iata' => $model->ail_iata, 'ail_lang' => $model->ail_lang], [
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
                'ail_iata',
                'ail_lang',
                'ail_name',
                'ail_city',
                'ail_country',
                'ail_created_user_id:username',
                'ail_updated_user_id:username',
                'ail_created_dt:byUserDateTime',
                'ail_updated_dt:byUserDateTime',
            ],
        ]) ?>

    </div>

</div>
