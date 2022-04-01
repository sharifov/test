<?php

namespace frontend\controllers;

use src\model\lead\reports\HeatMapLeadSearch;

/**
 * Class HeatMapLeadController
 */
class HeatMapLeadController extends FController
{
    public function init(): void
    {
        parent::init();
        $this->layoutCrud();
    }

    public function actionIndex()
    {
        $searchModel = new HeatMapLeadSearch(); /* TODO::  */

        return $this->render('index', [
            'searchModel' => $searchModel,
        ]);
    }
}
