<?php

namespace sales\interfaces;

interface Objectable
{
    public function getProjectId(): ?int;
    public function getDepartmentId(): ?int;
}
