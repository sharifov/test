<?php

use frontend\helpers\JsonHelper;
use yii\helpers\Html;
use yii\helpers\VarDumper;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var string $phone */
/** @var array $errors */
/** @var array $dbResult */
/** @var array $apiResult */

$this->title = 'Check Phone';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="check-phone">

    <h3><?= Html::encode($this->title) ?></h3>

    <?php Pjax::begin(['id' => 'pjax-check-phone', 'timeout' => 9000]); ?>
    <div class="row">
        <div class="col-md-2">

            <?= Html::beginForm(null, 'get', [
                'data-pjax' => 1
            ]) ?>
            <p>
            <?= Html::label('Phone number:') ?>

            <?= Html::input('text', 'phone', $phone, ['minlength' => 8, 'maxlength' => 50, 'style' => 'width: 100%;']) ?>
            </p>

            <?= Html::submitButton('Check Phone', ['class' => 'btn btn-primary']) ?>
            <?= Html::endForm() ?>
        </div>

        <div class="col-md-8">
            <?php if ($errors) : ?>
                <div class="alert alert-danger" role="alert">
                    <?php foreach ($errors as $value) : ?>
                        <p><?php echo $value ?></p>
                    <?php endforeach ?>
                </div>
            <?php endif ?>

            <?php if ($dbResult) : ?>
                <p>Db Result (PhoneServiceInfo):</p>
                <?php foreach ($dbResult as $value) : ?>
                    <?php foreach ($value['contactPhoneServiceInfos'] as $serviceInfo) : ?>
                        <?php $dataJson = JsonHelper::decode($serviceInfo['cpsi_data_json']) ?>
                        <pre><small><?php VarDumper::dump($dataJson, 20, true); ?></small></pre>
                    <?php endforeach ?>
                <?php endforeach ?>
            <?php endif ?>

            <?php if ($apiResult) : ?>
                <p>Api Neutrino Result:</p>
                <pre><small><?php VarDumper::dump($apiResult, 20, true); ?></small></pre>
            <?php endif ?>
        </div>

    </div>
    <?php Pjax::end(); ?>
</div>
