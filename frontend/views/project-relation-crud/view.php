<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\project\entity\projectRelation\ProjectRelation */

$this->title = $model->prl_project_id;
$this->params['breadcrumbs'][] = ['label' => 'Project Relations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="project-relation-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'prl_project_id' => $model->prl_project_id, 'prl_related_project_id' => $model->prl_related_project_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'prl_project_id' => $model->prl_project_id, 'prl_related_project_id' => $model->prl_related_project_id], [
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
                'prl_project_id:projectName',
                'prl_related_project_id:projectName',
                'prl_created_user_id:userName',
                'prl_updated_user_id:userName',
                'prl_created_dt:byUserDateTime',
                'prl_updated_dt:byUserDateTime',
            ],
        ]) ?>

    </div>

</div>
