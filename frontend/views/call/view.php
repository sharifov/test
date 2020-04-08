<?php

use common\models\Call;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Call */

$this->title = 'Call Id: ' . $model->c_id . ' ('.$model->c_from.' ... '.$model->c_to.')';
$this->params['breadcrumbs'][] = ['label' => 'Calls', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/wavesurfer.js/3.3.2/wavesurfer.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/wavesurfer.js/3.3.2/wavesurfer-html-init.min.js"></script>

<?php /*
<script src="https://cdnjs.cloudflare.com/ajax/libs/wavesurfer.js/3.3.2/plugin/wavesurfer.timeline.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/wavesurfer.js/3.3.2/plugin/wavesurfer.minimap.js"></script>
 */ ?>

<div class="call-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->c_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->c_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?php if($model->recordingUrl):?>
    <div class="col-md-12">
        <script>
            window.WS_InitOptions = {
                pluginCdnTemplate: "https://cdnjs.cloudflare.com/ajax/libs/wavesurfer.js/3.3.2/plugin/wavesurfer.[name].min.js",
                splitChannels: true
            };

        </script>


        <wavesurfer
                data-url="<?= $model->recordingUrl  ?>"
                data-plugins="minimap,timeline,cursor"
                data-split-channels="true"
                data-minimap-height="30"
                data-minimap-wave-color="#ddd"
                data-minimap-progress-color="#999"
                data-timeline-font-size="13px"
                data-timeline-container="#timeline"
        >
        </wavesurfer>

        <div id="timeline"></div>
        <br/>
    </div>
    <?php endif;?>

    <div class="col-md-6">

       <?php /* <audio id="myAudio" controls="controls" controlsList="nodownload" style="width: 100%;" class="video-js vjs-default-skin"><source src="<?= $model->recordingUrl ?>" type="audio/mpeg"></audio> */ ?>

        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'c_id',
                'c_call_sid',
                'c_parent_call_sid',

                [
                    'attribute' => 'c_call_type_id',
                    'value' => static function (\common\models\Call $model) {
                        return $model->getCallTypeName();
                    },
                ],
                'c_from',
                'c_to',
                'c_call_status',
                [
                    'attribute' => 'c_status_id',
                    'value' => static function (\common\models\Call $model) {
                        return $model->getStatusName();
                    },
                ],
                'c_forwarded_from',
                'c_caller_name',
                'c_call_duration',
                [
                    'attribute' => 'c_client_id',
                    'value' => static function (Call $model) {
                        return  $model->c_client_id ?: '-';
                    },
                ],
                [
                    'label' => 'Department',
                    'attribute' => 'c_dep_id',
                    'value' => static function (Call $model) {
                        return $model->cDep ? $model->cDep->dep_name : '-';
                    },
                ],
                [
                    'label' => 'UserGroups',
                    //'attribute' => 'c_dep_id',
                    'value' => static function (Call $model) {
                        $userGroupList = [];
                        if ($model->cugUgs) {
                            foreach ($model->cugUgs as $userGroup) {
                                $userGroupList[] =  '<span class="label label-info"><i class="fa fa-users"></i> ' . Html::encode($userGroup->ug_name) . '</span>';
                            }
                        }
                        return $userGroupList ? implode(' ', $userGroupList) : '-';
                    },
                    'format' => 'raw'
                ],



            ],
        ]) ?>


    </div>
    <div class="col-md-6">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'c_recording_sid',
            'c_recording_url:url',
            'c_recording_duration',

            /*[
                'attribute' => 'c_uri',
                'value' => static function (\common\models\Call $model) {
                    return $model->c_uri ? Html::a('Link', 'https://api.twilio.com'.$model->c_uri, ['target' => '_blank']) : '-';
                },
                'format' => 'raw'
            ],*/
            'c_sequence_number',
            'c_lead_id',

            [
                'attribute' => 'c_created_user_id',
                'value' => static function (\common\models\Call $model) {
                    return  $model->cCreatedUser ? '<i class="fa fa-user"></i> ' . Html::encode($model->cCreatedUser->username) : $model->c_created_user_id;
                },
                'format' => 'raw'
            ],
            //'c_created_dt',
            [
                'attribute' => 'c_created_dt',
                'value' => static function (\common\models\Call $model) {
                    return $model->c_created_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->c_created_dt)) : '-';
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'c_updated_dt',
                'value' => static function (\common\models\Call $model) {
                    return $model->c_updated_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->c_updated_dt)) : '-';
                },
                'format' => 'raw'
            ],


            'c_com_call_id',

            //'c_project_id',
            [
                'attribute' => 'c_project_id',
                'value' => static function (\common\models\Call $model) {
                    return $model->cProject ? $model->cProject->name : '-';
                },
                'filter' => \common\models\Project::getList()
            ],
            'c_error_message',
            'c_is_new:boolean',
            [
                'attribute' => 'c_price',
                'value' => static function (\common\models\Call $model) {
                    return $model->c_price ? '$'.number_format($model->c_price, 5) : '-';
                },
            ],
        ],
    ]) ?>
    </div>

</div>
