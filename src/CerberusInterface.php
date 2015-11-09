<?php

/**
 * Cerberus Interafce.
 *
 * @author  Leandro Silva <leandro@leandrosilva.info>
 * @license https://github.com/mt-olympus/cerberus/blob/master/LICENSE MIT Licence
 */
namespace Cerberus;

/**
 * Cerberus Interafce.
 *
 * @author  Leandro Silva <leandro@leandrosilva.info>
 * @license https://github.com/mt-olympus/cerberus/blob/master/LICENSE MIT Licence
 */
interface CerberusInterface
{
    const CLOSED = 0;
    const OPEN = 1;
    const HALF_OPEN = 2;

    /**
     * Is the circuit available to connections.
     *
     * @returns bool
     */
    public function isAvailable();

    /**
     * Returns the current status of the circuit.
     *
     * @return int CLOSED (0), OPEN (1) or HALF_OPEN (2)
     */
    public function getStatus();

    /**
     * Signals that the connection was failed, incrementing the failure count.
     */
    public function reportFailure();

    /**
     * Signals that the connection was a success, reseting the failure count.
     */
    public function reportSuccess();
}
