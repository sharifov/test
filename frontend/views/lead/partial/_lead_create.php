<?php

use common\widgets\Alert;
use frontend\models\LeadForm;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Pjax;

/**
 * @var View $this
 * @var LeadForm $leadForm
 */

?>

<div class="row">
	<div class="col-md-12 col-sm-12">
        <?php Pjax::begin([
            'id' => '_create_lead_2',
            'timeout' => 2000,
            'enablePushState' => false,
            'enableReplaceState' => false
        ]); ?>
		<?php $form = ActiveForm::begin([
			'id' => $leadForm->formName() . '-form',
			'enableClientValidation' => true,
			'action' => ['/lead/create2'],
            'options' => [
                'data-pjax' => 1
            ],
		]) ?>
		<div class="row">
            <div class="col-md-12">
                <?= Alert::widget() ?>
                <br>
                <?= $form->errorSummary($leadForm, ['showAllErrors' => false]) ?>
            </div>
            <div class="col-md-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Lead info</h2>
                        <ul class="nav navbar-right panel_toolbox">
<!--                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>-->
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <div class="col-md-12">
							<?= $this->render('_lead_create_preferences', [
								'form' => $form,
								'leadForm' => $leadForm
							])
							?>
                        </div>
                    </div>
                </div>
            </div>
			<div class="col-md-12 col-sm-12">
				<div class="x_panel">

					<div class="x_title">
						<h2>Client Info</h2>
						<ul class="nav navbar-right panel_toolbox">
<!--							<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>-->
						</ul>
						<div class="clearfix"></div>
					</div>
					<div class="x_content">
						<div class="col-md-12">
							<?= $this->render('_lead_create_client', [
								'form' => $form,
								'leadForm' => $leadForm,
							])
							?>
						</div>
                        <div class="col-md-12">
                        </div>
					</div>
				</div>
			</div>
            <div class="col-md-12 text-center">
                <?= Html::submitButton('<i class="fa fa-save"></i> Create Lead', ['class' => 'btn btn-success']) ?>
            </div>
		</div>
		<?php ActiveForm::end() ?>
        <?php Pjax::end(); ?>
	</div>
</div>
