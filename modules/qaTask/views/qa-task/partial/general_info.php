<?php

use yii\widgets\DetailView;

?>

<div class="x_panel">
    <div class="x_title">
        <h2><i class="fa fa-info"></i> General Info</h2>
        <ul class="nav navbar-right panel_toolbox">

        </ul>
        <div class="clearfix"></div>
    </div>
    <div class="x_content" style="display: block;">

        <?php if ($model): ?>
            <div class="row">
                <div class="col-md-6">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'project:projectName',
                            't_object_type_id:qaTaskObjectType',
                            't_object_id',
                            't_status_id:qaTaskStatus',
                            't_create_type_id:qaTaskCreatedType',
                            'category.tc_name',
                            't_department_id:department',
                            't_deadline_dt:byUserDateTime',
                            'assignedUser:userName',
                        ],
                    ]) ?>
                </div>
                <div class="col-md-6">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            't_rating:qaTaskRating',
                            't_description:ntext',
                            'createdUser:userName',
                            'updatedUser:userName',
                            't_created_dt:byUserDateTime',
                            't_updated_dt:byUserDateTime',
                        ],
                    ]) ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
