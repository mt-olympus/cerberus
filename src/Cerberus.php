<?php

namespace Cerberus;

use Zend\Cache\Storage\StorageInterface;

class Cerberus implements CerberusInterface
{
    private $storage;

    private $maxFailures;

    private $timeout;

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

    private function getFailures()
    {
        $success = false;
        $failures = (int) $this->storage->getItem('failures', $success);
        if (!$success) {
            $failures = 0;
            $this->storage->setItem('failures', $failures);
        }

        return $failures;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Cerberus\CerberusInterface::getStatus()
     */
    public function getStatus()
    {
        $failures = $this->getFailures();
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
