<?php

namespace src\services\cases;

use common\models\Client;
use common\models\Employee;
use common\models\Project;
use common\models\UserProjectParams;
use frontend\helpers\JsonHelper;
use src\entities\cases\Cases;
use src\model\project\entity\projectLocale\ProjectLocale;
use src\repositories\cases\CasesRepository;
use src\services\client\ClientManageService;
use src\services\TransactionManager;
use Yii;
use yii\helpers\ArrayHelper;

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
    ) {
        $this->casesRepository = $casesRepository;
        $this->clientManageService = $clientManageService;
        $this->transaction = $transaction;
    }

    /**
     * @param Project $project
     * @param Client $client
     * @param string|null $locale
     * @return array|mixed|null
     */
    public static function getLocaleParamsOld(Project $project, Client $client, ?string $locale) /* temporary do not remove */
    {
        $projectLocale = null;
        if ($client->cl_marketing_country) {
            $projectLocale = ProjectLocale::find()
                ->where(['pl_project_id' => $project->id])
                ->andWhere(['pl_language_id' => (string) $locale])
                ->andWhere(['pl_market_country' => (string) $client->cl_marketing_country])
                ->andWhere(['pl_enabled' => true])
                ->one();

            if (!$projectLocale) {
                $projectLocale = ProjectLocale::find()
                    ->where(['pl_project_id' => $project->id])
                    ->andWhere(['pl_language_id' => null])
                    ->andWhere(['pl_market_country' => (string) $client->cl_marketing_country])
                    ->andWhere(['pl_enabled' => true])
                    ->one();
            }
        } elseif ($client->cl_locale) {
            $projectLocale = ProjectLocale::find()
                ->where(['pl_project_id' => $project->id])
                ->andWhere(['pl_language_id' => $client->cl_locale])
                ->andWhere(['IS', 'pl_market_country', null])
                ->andWhere(['pl_enabled' => true])
                ->one();
        }

        if (!$projectLocale) {
            $projectLocale = ProjectLocale::find()
                ->where(['pl_project_id' => $project->id])
                ->andWhere(['pl_language_id' => null])
                ->andWhere(['pl_default' => true])
                ->andWhere(['IS NOT', 'pl_market_country', null])
                ->andWhere(['pl_enabled' => true])
                ->one();
        }

        if ($projectLocale) {
            return JsonHelper::decode($projectLocale->pl_params);
        }
        return [];
    }

    /**
     * @param int $projectId
     * @param string|null $marketingCountry
     * @param string|null $locale
     * @return array|mixed|null
     */
    public static function getLocaleParams(int $projectId, ?string $marketingCountry, ?string $locale)
    {
        if ($locale && $marketingCountry) {
            if ($params = self::searchParams($projectId, $locale, $marketingCountry)) {
                return $params;
            }
        }

        if ($locale && !$marketingCountry) {
            if ($defaultMarketCountry = ProjectLocale::getDefaultMarketCountryByProject($projectId)) {
                if ($params = self::searchParams($projectId, $locale, $defaultMarketCountry)) {
                    return $params;
                }
            }
        }

        if ($projectLocale = ProjectLocale::getDefaultProjectLocale($projectId)) {
            return JsonHelper::decode($projectLocale->pl_params);
        }
        return [];
    }

    private static function searchParams(int $projectId, ?string $language, ?string $marketingCountry): ?array
    {
        $projectLocale = ProjectLocale::getByProjectLanguageMarket($projectId, $language, $marketingCountry);
        if ($projectLocale) {
            return JsonHelper::decode($projectLocale->pl_params);
        }

        $projectLocaleByLocale = ProjectLocale::getByProjectLanguageMarket($projectId, $language, null);
        $projectLocaleByMarket = ProjectLocale::getByProjectLanguageMarket($projectId, null, $marketingCountry);

        if ($projectLocaleByLocale && $projectLocaleByMarket) {
            return ArrayHelper::merge(
                JsonHelper::decode($projectLocaleByLocale->pl_params),
                JsonHelper::decode($projectLocaleByMarket->pl_params)
            );
        }
        return null;
    }

    /**
     * @param Cases $case
     * @param Employee $user
     * @param string|null $locale
     * @return array
     */
    public function getEmailData(Cases $case, Employee $user, ?string $locale = null): array
    {
        $project = $case->project;
        $upp = null;

        if ($project) {
            $upp = UserProjectParams::find()->where(['upp_project_id' => $project->id, 'upp_user_id' => $user->id])->withEmailList()->withPhoneList()->one();
        }

        $content_data = $this->getEmailDataWithoutAgentData($case, $project, $locale);

        $content_data['agent'] = [
            'name'      => $user->full_name,
            'username'  => $user->username,
            'nickname'  => $user->nickname,
//            'phone'     => $upp && $upp->upp_tw_phone_number ? $upp->upp_tw_phone_number : '',
            'phone'     => $upp && $upp->getPhone() ? $upp->getPhone() : '',
//            'email'     => $upp && $upp->upp_email ? $upp->upp_email : '',
            'email'     => $upp && $upp->getEmail() ? $upp->getEmail() : '',
        ];

        return $content_data;
    }

    /**
     * @param Cases $case
     * @param Project|null $project
     * @param string|null $locale
     * @return array
     */
    public function getEmailDataWithoutAgentData(Cases $case, ?Project $project = null, ?string $locale = null): array
    {
        $project = $project ?? $case->project;
        $projectContactInfo = [];

        if ($project) {
            if ($project->contact_info) {
                $projectContactInfo = @json_decode($project->contact_info, true);
            }
        }
        $localeParams = [];
        if ($project && $client = $case->client) {
            $localeParams = self::getLocaleParams($project->id, $client->cl_marketing_country, $locale);
        }

        $content_data['case'] = [
            'id'  => $case->cs_id,
            'gid' => $case->cs_gid,
            'order_uid' => $case->cs_order_uid
        ];

        if ($case->caseSale) {
            $content_data['sales'] = array_column($case->caseSale, 'css_sale_book_id', 'css_sale_id');
        }

        $content_data['project'] = [
            'name'      => $project->name ?? '',
            'url'       => $project->link ?? 'https://',
            'address'   => $projectContactInfo['address'] ?? '',
            'phone'     => $projectContactInfo['phone'] ?? '',
            'email'     => $projectContactInfo['email'] ?? '',
        ];

        $content_data['localeParams'] = $localeParams;

        $content_data['client'] = [
            'fullName'     => $case->client->full_name ?? '',
            'firstName'    => $case->client->first_name ?? '',
            'lastName'     => $case->client->last_name ?? '',
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