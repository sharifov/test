<?php

namespace modules\product\src\entities\productQuote;

interface ProductQuoteChangeStatusInterface
{
    public function getId(): int;
    public function getStartStatus(): ?int;
    public function getEndStatus(): int;
    public function getDescription(): ?string;
    public function getActionId(): ?int;
    public function getOwnerId(): ?int;
    public function getCreatorId(): ?int;
}
