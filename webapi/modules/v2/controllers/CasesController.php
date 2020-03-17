<?php

namespace webapi\modules\v2\controllers;

use sales\model\cases\CaseCodeException;
use sales\model\cases\useCases\cases\api\create\CreateForm;
use sales\model\cases\useCases\cases\api\create\Handler;
use webapi\src\logger\ApiLogger;
use webapi\src\Messages;
use webapi\src\response\ErrorResponse;
use webapi\src\response\messages\CodeMessage;
use webapi\src\response\messages\DataMessage;
use webapi\src\response\messages\ErrorsMessage;
use webapi\src\response\messages\Message;
use webapi\src\response\messages\MessageMessage;
use webapi\src\response\messages\StatusCodeMessage;
use webapi\src\response\SuccessResponse;
use Yii;
use webapi\src\response\Response;

/**
 * Class CasesController
 *
 * @property Handler $createHandler
 */
class CasesController extends BaseController
{
    private $createHandler;

    public function __construct(
        $id,
        $module,
        ApiLogger $logger,
        Handler $createHandler,
        $config = []
    )
    {
        parent::__construct($id, $module, $logger, $config);
        $this->createHandler = $createHandler;
    }

    public function actionCreate(): Response
    {
        $form = new CreateForm($this->auth->au_project_id);

        if (!$form->load(Yii::$app->request->post())) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage(Messages::LOAD_DATA_ERROR),
                new ErrorsMessage('Not found Case data on POST request'),
                new CodeMessage(CaseCodeException::API_CASE_CREATE_NOT_FOUND_DATA_ON_REQUEST)
            );
        }

        if (!$form->validate()) {
            return new ErrorResponse(
                new MessageMessage(Messages::VALIDATION_ERROR),
                new ErrorsMessage($form->getErrors()),
                new CodeMessage(CaseCodeException::API_CASE_CREATE_VALIDATE)
            );
        }

        try {
            $result = $this->createHandler->handle($form->getDto());
        } catch (\Throwable $e) {
            return new ErrorResponse(
                new MessageMessage($e->getMessage()),
                new ErrorsMessage($e->getMessage()),
                new CodeMessage($e->getCode())
            );
        }

        return new SuccessResponse(
            new DataMessage(
                new Message('case_gid', $result->caseGid),
                new Message('client_uuid', $result->clientUuid),
            )
        );
    }
}
