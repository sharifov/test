<?php

namespace src\entities\serializer;

interface Serializable
{
    public function serialize(): array;
}
