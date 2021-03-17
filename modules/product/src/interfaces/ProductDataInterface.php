<?php

namespace modules\product\src\interfaces;

use common\models\Client;
use common\models\Lead;
use common\models\Project;
use modules\order\src\entities\order\Order;

/**
 * Interface ProductDataInterface
 */
interface ProductDataInterface
{
    public function getProject(): Project;
    public function getLead(): Lead;
    public function getClient(): Client;
    public function getOrder(): ?Order;
    public function getId(): int;
    public function serialize(): array;
}
