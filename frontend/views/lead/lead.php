<?php
/**
 * @var $leadForm LeadForm
 */
use yii\bootstrap\Html;
use frontend\models\LeadForm;

?>

<div class="page-header">
    <div class="container-fluid">
        <div class="page-header__wrapper">
            <h2 class="page-header__title"><?= Html::encode($this->title) ?></h2>
            <div class="page-header__general">
                <div class="page-header__general-item">
                    <strong>Lead Status:</strong>
                    <?php if ($leadForm->getLead()->isNewRecord) : ?>
                        <span id="status-label"><span class="label status-label label-info">New</span></span>
                    <?php else : ?>

                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="main-sidebars">
    <aside class="sidebar left-sidebar sl-client-sidebar">
        <?= $this->render('partial/_client', [
            'leadForm' => $leadForm
        ]);
        ?>
    </aside>

    <div class="panel panel-main">
        <?= $this->render('partial/_actions', [
            'leadForm' => $leadForm
        ]);
        ?>

        <div class="sl-request-content">
            <?= $this->render('partial/_flightDetails', [
                'leadForm' => $leadForm
            ]);
            ?>
        </div>
    </div>

    <aside class="sidebar right-sidebar sl-right-sidebar">
        <?= $this->render('partial/_preferences', [
            'leadForm' => $leadForm
        ]);
        ?>
    </aside>
</div>
