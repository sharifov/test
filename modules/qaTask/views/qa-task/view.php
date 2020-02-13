<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\qaTask\src\entities\qaTask\QaTask */

$this->title = 'Task ' . $model->t_id . ' [' . $model->t_gid . ']';
$this->params['breadcrumbs'][] = ['label' => 'Qa Tasks', 'url' => ['/qa-task/qa-task-queue/search']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="qa-task-view">

    <h1>
        <?= Html::encode($this->title) ?>
    </h1>

    <div class="x_panel">
        <div class="x_content" style="display: block;">
            <?= $this->render('partial/_actions', [
                'model' => $model,
            ])
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <?= $this->render('partial/_general_info', [
                'model' => $model,
            ])
            ?>
        </div>
    </div>
</div>
