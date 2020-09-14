<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\voiceMailRecord\entity\VoiceMailRecord */

$this->title = $model->vmr_call_id;
$this->params['breadcrumbs'][] = ['label' => 'Voice Mail Records', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/wavesurfer.js/3.3.2/wavesurfer.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/wavesurfer.js/3.3.2/wavesurfer-html-init.min.js"></script>
<div class="voice-mail-record-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-12">

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->vmr_call_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->vmr_call_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ]) ?>
        </p>

        <?php if($model->getRecordingUrl()):?>
            <div class="col-md-12">
                <script>
                    window.WS_InitOptions = {
                        pluginCdnTemplate: "https://cdnjs.cloudflare.com/ajax/libs/wavesurfer.js/3.3.2/plugin/wavesurfer.[name].min.js"
                    };
                </script>

                <wavesurfer
                        data-url="<?= $model->getRecordingUrl()  ?>"
                        data-plugins="minimap,timeline,cursor"

                        data-split-channels="true"
                        data-media-controls="false"

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

        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'vmr_call_id',
                'vmr_record_sid',
                'vmr_client_id:client',
                'user:userName',
                'vmr_created_dt:byUserDateTime',
                'vmr_duration:duration',
                'vmr_new:booleanByLabel',
                'vmr_deleted:booleanByLabel',
            ],
        ]) ?>

    </div>

</div>
