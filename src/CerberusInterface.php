<?php

namespace Cerberus;

interface CerberusInterface
{
    const CLOSED = 0;
    const OPEN = 1;
    const HALF_OPEN = 2;

    public function isAvailable();
    public function getStatus();
    public function reportFailure();
    public function reportSuccess();
}
