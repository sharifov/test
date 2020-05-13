<?php

use sales\services\parsingDump\worldSpan\WorldSpan;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $data array */
/* @var $dump string */
/* @var string $type */
/* @var string|null $typeDump */
/* @var bool|null $prepareSegment */

$this->title = 'Check Flight dump - GDS WorldSpan';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="check-flight-dump">

    <h3><?= Html::encode($this->title) ?></h3>


    <div class="col-md-4">
        <?= Html::beginForm() ?>
        <?= Html::dropDownList('type', $type,
            WorldSpan::TYPE_MAP,
            [
                'class' => 'form-control',
                'style' => 'margin-bottom: 12px; display:none;',
                'prompt' => '---',
            ])
        ?>
        <?= Html::textarea('dump', $dump, ['rows' => 10, 'style' => 'width: 100%']) ?><br><br>

        <?php echo Html::checkbox('prepare_segment', $prepareSegment,
                    ['id' => 'prepare_segment', ]) ?> Reservation prepare segment<br><br>

        <?= Html::submitButton('Check Flight', ['class' => 'btn btn-primary']) ?>
        <?= Html::endForm() ?>
    </div>


    <div class="col-md-8">
        <h2>Parse dump: <?php echo $typeDump ?></h2>
        <?php if ($data): ?>
            <pre>
            <?php \yii\helpers\VarDumper::dump($data, 10, true) ?>
            </pre>
        <?php endif; ?>
    </div>


</div>