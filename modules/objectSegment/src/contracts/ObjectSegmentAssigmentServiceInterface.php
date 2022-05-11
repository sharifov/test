<?php

namespace modules\objectSegment\src\contracts;

interface ObjectSegmentAssigmentServiceInterface
{
    public function assign(int $entityId, array $values): void;
}
