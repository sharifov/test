<?php

use src\model\clientChat\componentEvent\entity\ClientChatComponentEvent;
use yii\bootstrap4\Html;
use yii\helpers\StringHelper;
use yii\helpers\VarDumper;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model src\model\clientChat\componentEvent\entity\ClientChatComponentEvent */

$this->title = $model->getComponentEventName();
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Component Events', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
$formatter = new \common\components\i18n\Formatter();
?>
<div class="client-chat-component-event-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->ccce_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->ccce_id], [
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
                'ccce_id',
                [
                    'attribute' => 'ccce_chat_channel_id',
                    'value' => static function (ClientChatComponentEvent $model) {
                        return $model->chatChannel->ccc_name ?? null;
                    }
                ],
                [
                    'attribute' => 'ccce_component',
                    'value' => static function (ClientChatComponentEvent $model) {
                        return $model->getComponentEventName();
                    }
                ],
                [
                    'attribute' => 'ccce_event_type',
                    'value' => static function (ClientChatComponentEvent $model) {
                        return $model->getComponentTypeName();
                    }
                ],
                'ccce_enabled:booleanByLabel',
                'ccce_sort_order',
                'ccce_created_user_id:username',
                'ccce_updated_user_id:username',
                'ccce_created_dt:byUserDateTime',
                'ccce_updated_dt:byUserDateTime',
            ],
        ]) ?>

    </div>

    <div class="col-md-8">
        <h2>Component Config:</h2>
        <?php if ($model->ccce_component_config) : ?>
            <pre>
            <?php VarDumper::dump(@json_decode($model->ccce_component_config, true), 10, true) ?>
            </pre>
        <?php endif;?>
    </div>

    <div class="col-md-12">
        <h4>Component Rules</h4>
        <table class="table table-bordered table-hover table-condensed">
            <thead>
            <tr>
                <th style="width: 20px">#</th>
                <th class="text-center" style="width: 130px">Component Name</th>
                <th class="text-center" style="width: 130px">Value</th>
                <th class="text-center" style="width: 130px">Sort order</th>
                <th class="text-center" style="width: 130px">Enabled</th>
                <th class="text-center" style="width: 330px">Config</th>
            </tr>
            </thead>
            <tbody>

            <?php foreach ($model->componentRules as $key => $rule) : ?>
                <tr>
                    <td ><?= $key + 1 ?></td>
                    <td class="text-center"><?= $rule->getComponentName() ?></td>
                    <td class="text-center"><?= $rule->cccr_value ?></td>
                    <td class="text-center"><?= (int)$rule->cccr_sort_order ?></td>
                    <td class="text-center"><?= $formatter->asBooleanByLabel($rule->cccr_enabled) ?></td>
                    <td>
                        <?php
                            $content = '<p>' . StringHelper::truncate($rule->cccr_component_config, 216, '...', null, true) . '</p>';
                            $content .= Html::a(
                                '<i class="fas fa-eye"></i> details</a>',
                                null,
                                [
                                    'class' => 'btn btn-sm btn-success',
                                    'data-pjax' => 0,
                                    'onclick' => '(function ( $event ) { $("#data_' . $rule->cccr_component_event_id . $rule->cccr_value . $rule->cccr_runnable_component . '").toggle(); })();',
                                ]
                            );
                            $content .= $rule->cccr_component_config ?
                                '<pre id="data_' . $rule->cccr_component_event_id . $rule->cccr_value . $rule->cccr_runnable_component . '" style="display: none;">' .
                                VarDumper::dumpAsString(\frontend\helpers\JsonHelper::decode($rule->cccr_component_config), 10, true) . '</pre>' : '-';

                            echo $content;
                        ?>

                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>
