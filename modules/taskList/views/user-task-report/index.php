<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use modules\taskList\src\entities\TargetObject;
use modules\taskList\src\entities\userTask\UserTask;
use modules\taskList\src\entities\userTask\UserTaskHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\taskList\src\entities\userTask\UserTaskSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Task Report';
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
\frontend\assets\ChartJsAsset::register($this);
?>

<div class="user-task-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php Pjax::begin([
        'id' => 'pjax-user-task-report',
        'timeout' => 9999,
        'enablePushState' => true,
        'enableReplaceState' => false,
        'scrollTo' => 0,
    ]); ?>

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>&nbsp;</p>
    <?php /* TODO::  */ ?>

    <?php Pjax::end(); ?>

</div>
