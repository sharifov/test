<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use sales\model\callLog\entity\callLog\CallLog;
use sales\helpers\phone\MaskPhoneHelper;
use sales\model\call\abac\CallAbacObject;

/* @var $this yii\web\View */
/* @var $model sales\model\callLog\entity\callLog\CallLog */
/* @var $breadcrumbsPreviousPage string */
/* @var $breadcrumbsPreviousLabel string */

$this->title = $model->cl_id;
$this->params['breadcrumbs'][] = ['label' => $breadcrumbsPreviousLabel, 'url' => [$breadcrumbsPreviousPage]];
$this->params['breadcrumbs'][] = $this->title;

\yii\web\YiiAsset::register($this);
?>
<div class="call-log-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php /** @abac $callLogAbacDto, CallLogAbacObject::OBJ_CALL_LOG, CallLogAbacObject::ACTION_UPDATE, Call log act update */ ?>
        <?php if (Yii::$app->abac->can(null, CallAbacObject::OBJ_CALL_LOG, CallAbacObject::ACTION_UPDATE)) : ?>
            <?= Html::a('Update', ['update', 'id' => $model->cl_id], ['class' => 'btn btn-primary']) ?>
        <?php endif; ?>

        <?php /** @abac $callLogAbacDto, CallLogAbacObject::OBJ_CALL_LOG, CallLogAbacObject::ACTION_DELETE, Call log act delete */ ?>
        <?php if (Yii::$app->abac->can(null, CallAbacObject::OBJ_CALL_LOG, CallAbacObject::ACTION_DELETE)) : ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->cl_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ]) ?>
        <?php endif; ?>
    </p>

    <div class="row">
        <div class="col-md-4">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'cl_id',
                    'cl_group_id',
                    'cl_call_sid',
                    'cl_conference_id',
                    'cl_type_id:callLogType',
                    'cl_category_id:callLogCategory',
                    'cl_is_transfer:booleanByLabel',
                    'cl_duration',
                    //'cl_phone_from',
                    [
                        'attribute' => 'cl_phone_from',
                        'value' => static function (CallLog $model) {
                            if ($model->cl_type_id == \sales\model\callLog\entity\callLog\CallLogType::IN) {
                                return MaskPhoneHelper::masking($model->cl_phone_from);
                            }
                            return $model->cl_phone_from;
                        }
                    ],
                    //'cl_phone_to',
                    [
                        'attribute' => 'cl_phone_to',
                        'value' => static function (CallLog $model) {
                            if ($model->cl_type_id == \sales\model\callLog\entity\callLog\CallLogType::OUT) {
                                return MaskPhoneHelper::masking($model->cl_phone_to);
                            }
                            return $model->cl_phone_to;
                        }
                    ],
                    'phoneList.pl_phone_number',
                    'cl_user_id:userName',
                    'cl_department_id:department',
                    'cl_project_id:projectName',
                    'cl_call_created_dt:byUserDateTime',
                    'cl_call_finished_dt:byUserDateTime',
                    'cl_status_id:callLogStatus',
                    'cl_client_id:client',
                    'cl_price',
                    'cl_stir_status',
                ],
            ]) ?>
        </div>
    </div>

</div>
