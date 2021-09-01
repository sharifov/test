<?php

namespace sales\model\clientChat\componentEvent\component;

use sales\model\clientChat\componentEvent\component\defaultConfig\FlizzardSubscriptionDefaultConfig;
use sales\model\visitorSubscription\entity\VisitorSubscription;
use sales\model\visitorSubscription\repository\VisitorSubscriptionRepository;
use yii\helpers\VarDumper;

/**
 * Class CheckFlizzardSubscription
 * @package sales\model\clientChat\componentEvent\component
 *
 * @property-read VisitorSubscriptionRepository $visitorSubscriptionRepository
 */
class FlizzardSubscription implements ComponentEventInterface
{
    /**
     * @var VisitorSubscriptionRepository
     */
    private VisitorSubscriptionRepository $visitorSubscriptionRepository;

    public function __construct(VisitorSubscriptionRepository $visitorSubscriptionRepository)
    {
        $this->visitorSubscriptionRepository = $visitorSubscriptionRepository;
    }

    public function run(ComponentDTOInterface $dto): string
    {
        if ($subscription = VisitorSubscription::find()->byUid((string)$dto->getVisitorId())->enabled()->byType(VisitorSubscription::SUBSCRIPTION_FLIZZARD)->one()) {
            return 'true';
        }
        return 'false';
    }

    public function getDefaultConfig(): array
    {
        return FlizzardSubscriptionDefaultConfig::getConfig();
    }

    public function getDefaultConfigJson(): string
    {
        return FlizzardSubscriptionDefaultConfig::getConfigJson();
    }
}
