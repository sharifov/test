<?php

namespace modules\hotel\controllers;

use frontend\controllers\FController;
use yii\web\Controller;

/**
 * Default controller for the `hotel` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
}
