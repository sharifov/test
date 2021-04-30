<?php

use common\models\Project;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Project */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Projects', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="project-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <div class="row">
        <div class="col-md-5" style="margin-right: 20px;">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'project_key',
                    'name:projectName',
                    'link:url',
                    'api_key',
                    'email_postfix',
                    'ga_tracking_id',
                    //'contact_info:ntext',
                    'closed:boolean',
                    'p_update_user_id:userName',
                    'last_update',
                    'sort_order',
                    [
                        'label' => 'Relations',
                        'value' => static function (Project $project) {
                            if (!$project->projectRelations) {
                                return Yii::$app->formatter->nullDisplay;
                            }
                            $result = [];
                            foreach ($project->projectRelations as $key => $value) {
                                $result[] = Yii::$app->formatter->asProjectName($value->prl_related_project_id);
                            }
                            return implode(' ', $result);
                        },
                        'format' => 'raw',
                    ],
                ],
            ]) ?>
        </div>
        <div class="col-md-6 bg-white">
            <h2>Contact info:</h2>
            <?=\yii\helpers\VarDumper::dumpAsString(\yii\helpers\Json::decode($model->contact_info), 10, true) ?>
        </div>
    </div>
    <br clear="all" />

    <div class="row">

        <div class="col-md-5 bg-white">
            <h2>Parameters:</h2>
            <?=\yii\helpers\VarDumper::dumpAsString($model->p_params_json, 10, true) ?>
        </div>
    </div>

</div>
