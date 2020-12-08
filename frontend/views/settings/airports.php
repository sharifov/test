<?php

/**
 * @var $this \yii\web\View
 * @var $dataProvider ActiveDataProvider
 * @var $searchModel AirportForm
 */

use common\models\Employee;
use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Url;
use frontend\models\search\AirportForm;
use common\models\Airports;
use yii\widgets\Pjax;

$this->title = 'Airports';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="settings-airports">
<h1><?= Html::encode($this->title) ?></h1>
<?php Pjax::begin(); ?>

    <p>
        <?= Html::a('Sync Airports', '#', [
            'class' => 'btn-success btn sync',
            'data-url' => Url::to([
                'settings/sync',
                'type' => 'airports'
            ])
        ]) ?>

        <?= Html::a('Synchronization Airports from TravelServices', ['synchronization'], ['class' => 'btn btn-warning', 'data' => [
            'confirm' => 'Are you sure you want synchronization all airports from TravelServices?',
            'method' => 'post',
        ],]) ?>

    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'iata',
            'name',
            'city',
            'country',
            'dst'
        ]
    ])
?>
    <?php Pjax::end(); ?>
</div>