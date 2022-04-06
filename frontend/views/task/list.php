<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\TaskSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Task List';
$this->params['breadcrumbs'][] = $this->title;

$bundle = \frontend\assets\FullCalendarAsset::register($this);
?>
<div class="task-list">

    <h1><i class="fa fa-tasks"></i> <?= Html::encode($this->title) ?></h1>

    <div class="col-md-6">
        <div id='calendar'></div>
    </div>


    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php /*= Html::a('Create Task', ['create'], ['class' => 'btn btn-success'])*/ ?>
    </p>

    <?php /*= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            't_id',
            't_key',
            't_name',
            [
                'attribute' => 't_category_id',
                'value' => function (\common\models\Task $model) {
                    return $model->getCategoryName();
                },
                'filter' => \common\models\Task::CAT_LIST
            ],

            't_description',
            't_hidden:boolean',
            't_sort_order',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); */?>
    <?php Pjax::end(); ?>
</div>

<?php
$js = <<<JS
    var calendarEl = document.getElementById('calendar');


    var calendar = new FullCalendar.Calendar(calendarEl, {
      headerToolbar: {
        center: 'dayGridMonth,timeGridFourDay' // buttons for switching between views
      },
      views: {
        timeGridFourDay: {
          type: 'timeGrid',
          duration: { days: 7 },
          buttonText: '7 day'
        }
      },
      visibleRange: function(currentDate) {
    // Generate a new date for manipulating in the next step
    var startDate = new Date(currentDate.valueOf());
    var endDate = new Date(currentDate.valueOf());

    // Adjust the start & end dates, respectively
    startDate.setDate(startDate.getDate() - 1); // One day in the past
    endDate.setDate(endDate.getDate() + 2); // Two days into the future

    return { start: startDate, end: endDate };
  }
    });

    // var calendar = new FullCalendar.Calendar(calendarEl, {
    //     initialView: 'dayGridMonth'
    // });
    calendar.render();
JS;

$this->registerJs($js);
