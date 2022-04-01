<?php

namespace frontend\controllers;



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
        $searchModel = null; /* TODO::  */

        return $this->render('index', [
            'searchModel' => $searchModel,
        ]);
    }
}
