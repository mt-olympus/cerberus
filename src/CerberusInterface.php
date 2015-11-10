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
     * @param string|null $serviceName The service name
     *
     * @returns bool
     */
    public function isAvailable($serviceName = null);

    /**
     * Returns the current status of the circuit.
     *
     * @param string|null $serviceName The service name
     *
     * @return int CLOSED (0), OPEN (1) or HALF_OPEN (2)
     */
    public function getStatus($serviceName = null);

    /**
     * Signals that the connection was failed, incrementing the failure count.
     *
     * @param string|null $serviceName The service name
     */
    public function reportFailure($serviceName = null);

    /**
     * Signals that the connection was a success, reseting the failure count.
     *
     * @param string|null $serviceName The service name
     */
    public function reportSuccess($serviceName = null);
}
