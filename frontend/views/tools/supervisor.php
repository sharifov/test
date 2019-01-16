<?php

use yii\helpers\Html;
use yii\grid\GridView;
use dosamigos\datepicker\DatePicker;

/* @var $this yii\web\View */


$this->title = 'Supervisor ';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="supervisor">

    <h1><?= Html::encode($this->title) ?></h1>

    <iframe src="/supervisor/" width="100%" height="100%" frameborder="1" sandbox="allow-scripts"></iframe>
</div>