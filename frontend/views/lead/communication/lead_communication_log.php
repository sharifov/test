<?php

/**
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $comForm CommunicationForm
 * @var $leadForm LeadForm
 * @var $previewEmailForm LeadPreviewEmailForm
 * @var $previewSmsForm LeadPreviewSmsForm
 * @var $isAdmin bool
 *
 */

use frontend\models\CommunicationForm;
use frontend\models\LeadForm;
use frontend\models\LeadPreviewEmailForm;
use frontend\models\LeadPreviewSmsForm;

$c_type_id = $comForm->c_type_id;
?>

<div class="x_panel">
    <div class="x_title">
        <h2><i class="fa fa-comments"></i> Communication Log</h2>
        <ul class="nav navbar-right panel_toolbox">
            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
        </ul>
        <div class="clearfix"></div>
    </div>
    <div class="x_content">
        <?php \yii\widgets\Pjax::begin(['id' => 'pjax-lead-communication-log', 'timeout' => 5000]) ?>
            <div class="panel">
                <div class="chat__list">
                    <?= \yii\widgets\ListView::widget([
                        'dataProvider' => $dataProvider,

                        'options' => [
                            'tag' => 'div',
                            'class' => 'list-wrapper',
                            'id' => 'list-wrapper',
                        ],
                        'emptyText' => '<div class="text-center">Not found communication messages</div><br>',
                        'layout' => "{summary}\n<div class=\"text-center\">{pager}</div>\n{items}<div class=\"text-center\">{pager}</div>\n",
                        'itemView' => function ($model, $key, $index, $widget) use ($dataProvider) {
                            return $this->render('_list_item_log', ['model' => $model, 'dataProvider' => $dataProvider]);
                        },

                        'itemOptions' => [
                            //'class' => 'item',
                            'tag' => false,
                        ],

                    ]) ?>
                </div>
            </div>
        <?php \yii\widgets\Pjax::end() ?>
    </div>
</div>
