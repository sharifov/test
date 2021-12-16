<?php

use common\components\grid\DateTimeColumn;
use modules\flight\models\FlightRequest;
use common\components\grid\ApiUserColumn;
use common\components\grid\project\ProjectColumn;
use yii\grid\ActionColumn;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\flight\models\search\FlightRequestSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Flight Requests';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="flight-request-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php // echo Html::a('Create Flight Request', ['create'], ['class' => 'btn btn-success'])?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-flight-request', 'scrollTo' => 0]); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{errors}\n{summary}\n{items}\n{pager}",
        'columns' => [

            'fr_id',
            'fr_booking_id',
            'fr_hash',
            [
                'attribute' => 'fr_type_id',
                'value' => static function (FlightRequest $model) {
                    return $model->getTypeName();
                },
                'filter' => FlightRequest::TYPE_LIST
            ],
            [
                'attribute' => 'fr_status_id',
                'value' => static function (FlightRequest $model) {
                    return $model->getStatusName();
                },
                'filter' => FlightRequest::STATUS_LIST
            ],
            [
                'class' => ProjectColumn::class,
                'attribute' => 'fr_project_id',
                'relation' => 'project',
            ],
            [
                'class' => ApiUserColumn::class,
                'attribute' => 'fr_created_api_user_id',
                'relation' => 'apiUser',
            ],
            'fr_job_id',
            ['class' => DateTimeColumn::class, 'attribute' => 'fr_created_dt'],
            //'fr_updated_dt',
            //'fr_year',
            //'fr_month',

            ['class' => ActionColumn::class],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
