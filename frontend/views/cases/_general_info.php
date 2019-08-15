<?php

use sales\entities\cases\Cases;
use sales\entities\cases\CasesStatusHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model \sales\entities\cases\Cases */
/* @var $isAdmin boolean */
?>

<div class="x_panel">
    <div class="x_title">
        <h2><i class="fa fa-info"></i> General Info</h2>
        <ul class="nav navbar-right panel_toolbox">
            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
            </li>
            <?/*<li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                <ul class="dropdown-menu" role="menu">
                    <li><a href="#">Settings 1</a>
                    </li>
                    <li><a href="#">Settings 2</a>
                    </li>
                </ul>
            </li>
            <li><a class="close-link"><i class="fa fa-close"></i></a>
            </li>*/?>
        </ul>
        <div class="clearfix"></div>
    </div>
    <div class="x_content" style="display: block;">
        <?php if($model):?>
        <div class="row">
            <div class="col-md-6">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'cs_id',
                        [
                            'attribute' => 'cs_status',
                            'value' => function (Cases $model) {
                                return CasesStatusHelper::getName($model->cs_status);
                            },
                        ],
                        [
                            'attribute' => 'cs_project_id',
                            'value' => function (Cases $model) {
                                return $model->project ? $model->project->name : '';
                            },
                        ],
                        [
                            'attribute' => 'cs_user_id',
                            'value' => function (Cases $model) {
                                return $model->owner ? $model->owner->username : '';
                            },
                        ],
                        'cs_subject',
                        'cs_description:ntext',
                    ],
                ]) ?>
            </div>
            <div class="col-md-6">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'cs_category',
                        'cs_lead_id',
                        'cs_call_id',
                        'cs_dep_id',
                        //'cs_client_id',
                        'cs_created_dt',
                        //'cs_updated_dt',
                    ],
                ]) ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

