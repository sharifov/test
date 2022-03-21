<?php

namespace modules\rentCar\controllers;

use Exception;
use frontend\controllers\FController;
use modules\product\src\entities\productType\ProductType;
use modules\rentCar\src\forms\RentCarUpdateRequestForm;
use modules\rentCar\src\repositories\rentCar\RentCarRepository;
use src\auth\Auth;
use Yii;
use modules\rentCar\src\entity\rentCar\RentCar;
use modules\rentCar\src\entity\rentCar\RentCarSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;

/**
 * Class RentCarController
 */
class RentCarController extends FController
{
    public function init(): void
    {
        parent::init();
        $this->layoutCrud();
    }

    public function actionUpdateAjax()
    {
        $id = Yii::$app->request->get('id');

        try {
            $rentCar = $this->findModel($id);
        } catch (\Throwable $throwable) {
            return '<script>alert("' . $throwable->getMessage() . '")</script>';
        }

        $form = new RentCarUpdateRequestForm($rentCar);

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $out = '<script>$("#modal-sm").modal("hide"); 
                    pjaxReload({container: "#pjax-product-search-' . $rentCar->prc_product_id . '"});
                    pjaxReload({container: "#pjax-lead-offers"}); ';
            try {
                $rentCar->prc_pick_up_code = $form->pick_up_code;
                $rentCar->prc_pick_up_date = $form->pick_up_date;
                $rentCar->prc_drop_off_code = $form->drop_off_code;
                $rentCar->prc_drop_off_date = $form->drop_off_date;
                $rentCar->prc_pick_up_time = $form->pick_up_time;
                $rentCar->prc_drop_off_time = $form->drop_off_time;

                $rentCarRepository = Yii::createObject(RentCarRepository::class);
                $rentCarRepository->save($rentCar);

                $out .= 'createNotifyByObject({title: "Rent Car update request", type: "success", text: "Success" , hide: true});';
            } catch (\DomainException $e) {
                $out .= 'createNotifyByObject({title: "Rent Car update request", type: "error", text: "' . $e->getMessage() . '" , hide: true});';
            } catch (\Throwable $e) {
                $out .= 'createNotifyByObject({title: "Rent Car update request", type: "error", text: "Server error" , hide: true});';
                Yii::error($e, 'RentCarController:actionUpdateAjax');
            }

            return $out . '</script>';
        }

        return $this->renderAjax('update_ajax', [
            'modelForm' => $form,
            'rentCar' => $rentCar,
        ]);
    }

    /**
     * @param integer $id
     * @return RentCar
     * @throws NotFoundHttpException
     */
    protected function findModel($id): RentCar
    {
        if (($model = RentCar::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
