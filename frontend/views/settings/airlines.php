<?php
/**
 * @var $this \yii\web\View
 * @var $dataProvider ActiveDataProvider
 * @var $searchModel AirlineForm
 */

use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Url;
use backend\models\search\AirlineForm;
use common\models\Airline;

$template = <<<HTML
<div class="pagination-container row" style="margin-bottom: 10px;">
    <div class="col-sm-4" style="/*padding-top: 20px;*/">
        {summary}
    </div>
    <div class="col-sm-8" style="text-align: right;">
       {pager}
    </div>
</div>
<div class="table-responsive">
    {items}
</div>
HTML;

?>

<div class="panel panel-default">
    <div class="panel-heading">Airlines</div>
    <div class="panel-body">
        <?php if (Yii::$app->user->identity->role == 'admin') : ?>
            <div class="mb-20">
                <?= Html::a('Sync Airlines', '#', [
                    'class' => 'btn-success btn sync',
                    'data-url' => Url::to([
                        'settings/sync',
                        'type' => 'airlines'
                    ])
                ]) ?>
            </div>
        <?php endif; ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            //'layout' => $template,
            'filterModel' => $searchModel,
            'columns' => [
                'iata',
                'name',
                'countryCode',
                'country',
            ]
        ])
        ?>
    </div>
</div>