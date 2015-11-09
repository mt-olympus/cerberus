<?php

/**
 * The Cerberus class.
 *
 * @author  Leandro Silva <leandro@leandrosilva.info>
 * @license https://github.com/mt-olympus/cerberus/blob/master/LICENSE MIT Licence
 */
namespace Cerberus;

use Zend\Cache\Storage\StorageInterface;

/**
 * The Cerberus Class.
 *
 * @author  Leandro Silva <leandro@leandrosilva.info>
 * @license https://github.com/mt-olympus/cerberus/blob/master/LICENSE MIT Licence
 */
class Cerberus implements CerberusInterface
{
    /**
     * The storage object.
     *
     * @var \Zend\Cache\Storage\StorageInterface
     */
    private $storage;

    /**
     * Maximum number of failures to open the circuit.
     *
     * @var int
     */
    private $maxFailures;

    /**
     * Number of seconds to change from OPEN to HALF OPEN and try the connection again.
     *
     * @var int
     */
    private $timeout;

    /**
     * Constructor.
     *
     * @param \Zend\Cache\Storage\StorageInterface $storage     The storage object
     * @param int                                  $maxFailures Maximum number of failures to open the circuit
     * @param int                                  $timeout     Number of seconds to change from OPEN to HALF OPEN and try the connection again
     */
    public function __construct(StorageInterface $storage, $maxFailures = 5, $timeout = 30)
    {
        $this->maxFailures = (int) $maxFailures;
        $this->timeout = (int) $timeout;
        $this->storage = $storage;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Cerberus\CerberusInterface::isAvailable()
     */
    public function isAvailable()
    {
        return $this->getStatus() !== CerberusInterface::OPEN;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Cerberus\CerberusInterface::getStatus()
     */
    public function getStatus()
    {
        $success = false;
        $failures = (int) $this->storage->getItem('failures', $success);
        if (!$success) {
            $failures = 0;
            $this->storage->setItem('failures', $failures);
        }

        // Still has failures left
        if ($failures < $this->maxFailures) {
            return CerberusInterface::CLOSED;
        }

        $success = false;
        $lastAttempt = $this->storage->getItem('last_attempt', $success);

        // This is the first attempt after a failure, open the circuit
        if (!$success) {
            $lastAttempt = time();
            $this->storage->setItem('last_attempt', $lastAttempt);

            return CerberusInterface::OPEN;
        }

        // Reached maxFailues but has passed the timeout limit, so we can try again
        // We update the lastAttempt so only one call passes through
        if (time() - $lastAttempt >= $this->timeout) {
            $lastAttempt = time();
            $this->storage->setItem('last_attempt', $lastAttempt);

            return CerberusInterface::HALF_OPEN;
        }

        return CerberusInterface::OPEN;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Cerberus\CerberusInterface::reportFailure()
     */
    public function reportFailure()
    {
        $this->storage->incrementItem('failures', 1);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Cerberus\CerberusInterface::reportSuccess()
     */
    public function reportSuccess()
    {
        $this->storage->setItem('failures', 0);
    }
}
