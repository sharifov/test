<?php

use modules\smartLeadDistribution\src\SmartLeadDistribution;
use yii\helpers\Html;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var integer $leadId */
/** @var array $dataRating */
/** @var array $errors */

$this->title = 'Check Lead Rating';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="check-phone">

    <h3><?= Html::encode($this->title) ?></h3>

    <?php Pjax::begin(['id' => 'pjax-lead-rating', 'timeout' => 9000]); ?>
    <div class="row">
        <div class="col-md-2">
            <?php if ($errors) : ?>
                <div class="alert alert-danger" role="alert">
                    <?php foreach ($errors as $value) : ?>
                        <p><?php echo $value ?></p>
                    <?php endforeach ?>
                </div>
            <?php endif ?>

            <?= Html::beginForm(null, 'get', [
                'data-pjax' => 1
            ]) ?>

            <div class="form-group">
                <?= Html::label('Lead ID:') ?>
                <?= Html::input('text', 'leadId', $leadId, ['minlength' => 1, 'style' => 'width: 100%;']) ?>
            </div>

            <?= Html::submitButton('Count lead rating', ['class' => 'btn btn-primary']) ?>
            <?= Html::endForm() ?>
        </div>

        <div class="col-md-5">
            <?php if (!empty($dataRating)) : ?>
                <h3>Lead rating: <?= $dataRating['points'] ?>; Category: <?= SmartLeadDistribution::CATEGORY_LIST[$dataRating['category']] ?></h3>

                <?php if (!empty($dataRating['log'])) : ?>
                    <?php foreach ($dataRating['log'] as $value) : ?>
                        <h6><?= $value ?></h6>
                    <?php endforeach ?>
                <?php endif; ?>
            <?php endif ?>
        </div>
    </div>
    <?php Pjax::end(); ?>
</div>
