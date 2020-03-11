<?php

namespace sales\services\cases;

use common\models\Employee;
use common\models\UserProjectParams;
use sales\entities\cases\Cases;
use sales\repositories\cases\CasesRepository;
use sales\services\client\ClientManageService;
use sales\services\TransactionManager;
use Yii;

/**
 * Class CasesCommunicationService
 *
 * @property CasesRepository $casesRepository
 * @property ClientManageService $clientManageService
 * @property TransactionManager $transaction
 */
class CasesCommunicationService
{
    public const TYPE_PROCESSING_EMAIL = 'email';
    public const TYPE_PROCESSING_SMS = 'sms';
    public const TYPE_PROCESSING_CALL = 'call';

    private $casesRepository;
    private $clientManageService;
    private $transaction;

    /**
     * CasesCommunicationService constructor.
     * @param CasesRepository $casesRepository
     * @param ClientManageService $clientManageService
     * @param TransactionManager $transaction
     */
    public function __construct(
        CasesRepository $casesRepository,
        ClientManageService $clientManageService,
        TransactionManager $transaction
    )
    {
        $this->casesRepository = $casesRepository;
        $this->clientManageService = $clientManageService;
        $this->transaction = $transaction;
    }

    /**
     * @param Cases $case
     * @param Employee $user
     * @return array
     */
    public function getEmailData(Cases $case, Employee $user): array
    {
        $project = $case->project;
        $projectContactInfo = [];
        $upp = null;

        if ($project) {
            $upp = UserProjectParams::find()->where(['upp_project_id' => $project->id, 'upp_user_id' => $user->id])->one();
            if ($project && $project->contact_info) {
                $projectContactInfo = @json_decode($project->contact_info, true);
            }
        }

        $content_data['case'] = [
            'id'  => $case->cs_id,
            'gid' => $case->cs_gid
        ];

        $content_data['project'] = [
            'name'      => $project ? $project->name : '',
            'url'       => $project ? $project->link : 'https://',
            'address'   => $projectContactInfo['address'] ?? '',
            'phone'     => $projectContactInfo['phone'] ?? '',
            'email'     => $projectContactInfo['email'] ?? '',
        ];

        $content_data['agent'] = [
            'name'      => $user->full_name,
            'username'  => $user->username,
            'phone'     => $upp && $upp->upp_tw_phone_number ? $upp->upp_tw_phone_number : '',
            'email'     => $upp && $upp->upp_email ? $upp->upp_email : '',
        ];

        $content_data['client'] = [
            'fullName'     => $case->client ? $case->client->full_name : '',
            'firstName'    => $case->client ? $case->client->first_name : '',
            'lastName'     => $case->client ? $case->client->last_name : '',
        ];

        return $content_data;
    }

    public function processIncoming(Cases $case, string $typeProcessing): void
    {
        if ($case->isTrash() || $case->isFollowUp()) {
            try {
                if (!$case->isFreedOwner()) {
                    $case->freedOwner();
                }
                $case->pending(null, null);
                $this->casesRepository->save($case);
            } catch (\Throwable $e) {
                Yii::error($e, 'CasesCommunicationService:processIncoming' . ':type:' . $typeProcessing);
            }
        }
    }
}
