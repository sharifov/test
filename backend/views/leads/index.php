<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LeadSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Search Leads';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?//= Html::a('Create Lead', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'id',
                'options' => ['style' => 'width:80px'],
                'contentOptions' => ['class' => 'text-center'],
            ],

            [
                'attribute' => 'uid',
                'options' => ['style' => 'width:100px'],
                'contentOptions' => ['class' => 'text-center'],
            ],

            [   'attribute' => 'client_id',
                'options' => ['style' => 'width:80px'],
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                //'attribute' => 'client_id',
                'header' => 'Client name',
                'format' => 'raw',
                'value' => function(\common\models\Lead $model) {
                    return $model->client ? '<i class="fa fa-user"></i> ' . Html::encode($model->client->first_name.' '.$model->client->last_name) : '-';
                },
                'options' => ['style' => 'width:160px'],
                //'filter' => \common\models\Employee::getList()
            ],

            [
                'header' => 'Client Emails/Phones',
                'format' => 'raw',
                'value' => function(\common\models\Lead $model) {
                    $str = $model->client && $model->client->clientEmails ? '<i class="fa fa-envelope"></i> '.implode(' <br><i class="fa fa-envelope"></i> ', \yii\helpers\ArrayHelper::map($model->client->clientEmails, 'email', 'email')).'' : '';
                    $str .= $model->client && $model->client->clientPhones ? '<br><i class="fa fa-phone"></i> '.implode(' <br><i class="fa fa-phone"></i> ', \yii\helpers\ArrayHelper::map($model->client->clientPhones, 'phone', 'phone')).'' : '';

                    return $str ?? '-';
                },
                'options' => ['style' => 'width:180px'],
            ],

            /*[
                'header' => 'Client Phones',
                'value' => function(\common\models\Lead $model) {
                    return $model->client && $model->client->clientPhones ? implode(', ', \yii\helpers\ArrayHelper::map($model->client->clientPhones, 'phone', 'phone')) : '-';
                },
            ],*/

            //'employee_id',
            //'status',
            [
                'attribute' => 'status',
                'value' => function(\common\models\Lead $model) {
                    return '<h4><span class="label '.$model->getLabelClass().'">'.\common\models\Lead::getStatus($model->status).'</span></h4>';
                },
                'format' => 'raw',
                'filter' => \common\models\Lead::STATUS_LIST
            ],
            [
                'attribute' => 'project_id',
                'value' => function(\common\models\Lead $model) {
                    return $model->project ? $model->project->name : '-';
                },
                'filter' => \common\models\Project::getList()
            ],


            //'project_id',
            //'source_id',
            [
                'attribute' => 'source_id',
                'value' => function(\common\models\Lead $model) {
                    return $model->source ? $model->source->name : '-';
                },
                'filter' => \common\models\Source::getList()
            ],

            [
                'attribute' => 'trip_type',
                'value' => function(\common\models\Lead $model) {
                    return \common\models\Lead::getFlightType($model->trip_type) ?? '-';
                },
                'filter' => \common\models\Lead::TRIP_TYPE_LIST
            ],

            [
                'attribute' => 'cabin',
                'value' => function(\common\models\Lead $model) {
                    return \common\models\Lead::getCabin($model->cabin) ?? '-';
                },
                'filter' => \common\models\Lead::CABIN_LIST
            ],

            //'trip_type',
            //'cabin',
            //'adults',

            [
                'attribute' => 'adults',
                'value' => function(\common\models\Lead $model) {
                    return $model->adults ?: 0;
                },
                'filter' => array_combine(range(0, 9), range(0, 9)),
                'contentOptions' => ['class' => 'text-center'],
            ],

            [
                'attribute' => 'children',
                'value' => function(\common\models\Lead $model) {
                    return $model->children ?: '-';
                },
                'filter' => array_combine(range(0, 9), range(0, 9)),
                'contentOptions' => ['class' => 'text-center'],
            ],

            [
                'attribute' => 'infants',
                'value' => function(\common\models\Lead $model) {
                    return $model->infants ?: '-';
                },
                'filter' => array_combine(range(0, 9), range(0, 9)),
                'contentOptions' => ['class' => 'text-center'],
            ],

            //'children',
            //'infants',
            //'notes_for_experts:ntext',

            //'updated',
            //'request_ip',
            //'request_ip_detail:ntext',

            [
                'attribute' => 'employee_id',
                'format' => 'raw',
                'value' => function(\common\models\Lead $model) {
                    return $model->employee ? '<i class="fa fa-user"></i> '.$model->employee->username : '-';
                },
                'filter' => \common\models\Employee::getList()
            ],

            //'rating',
            //'called_expert',
            /*[
                'attribute' => 'discount_id',
                'options' => ['style' => 'width:100px'],
                'contentOptions' => ['class' => 'text-center'],
            ],*/
            //'offset_gmt',
            //'snooze_for',
            //'created',
            [
                'attribute' => 'created',
                'format' => 'raw',
                'value' => function(\common\models\Lead $model) {
                    return $model->created ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime($model->created) : '-';
                },
            ],
            //'bo_flight_id',

            ['class' => 'yii\grid\ActionColumn', 'template' => '{view}'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
