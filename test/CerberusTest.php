<?php

namespace Cerberustest;

use Zend\Cache\StorageFactory;
use Cerberus\CerberusInterface;
use Cerberus\Cerberus;
use Cerberus\Factory;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\Config;

class CerberusTest extends \PHPUnit_Framework_TestCase
{
    private $cerberus;

    public function setUp()
    {
        $storage = StorageFactory::factory([
            'adapter' => [
                'name' => 'filesystem',
                'options' => [
                    'cache_dir' => 'data/cache',
                    'namespace' => 'test',
                ],
            ],
            'plugins' => [
                // Don't throw exceptions on cache errors
                'exception_handler' => [
                    'throw_exceptions' => true,
                ],
                //'Serializer',
            ],
        ]);

        $storage->flush();

        $this->cerberus = new Cerberus($storage, 2, 2);
    }

    public function testFactory()
    {
        $sm = new ServiceManager(new Config());
        $sm->setService('config', []);
        $this->assertInstanceOf(Cerberus::class, (new Factory())->__invoke($sm));

        $sm = new ServiceManager(new Config());
        $sm->setService('config', ['cerberus' => [
            'storage' => [
            'adapter' => [
                'name' => 'filesystem',
                'options' => [
                    'cache_dir' => 'data/cache',
                    'namespace' => 'test',
                ],
            ],
            'plugins' => [
                // Don't throw exceptions on cache errors
                'exception_handler' => [
                    'throw_exceptions' => true,
                ],
            ], ], ]]);
        $this->assertInstanceOf(Cerberus::class, (new Factory())->__invoke($sm));
    }

    public function testCreatedClosed()
    {
        $this->assertSame(CerberusInterface::CLOSED, $this->cerberus->getStatus());
        $this->assertTrue($this->cerberus->isAvailable());
    }

    public function testFailure()
    {
        $this->cerberus->reportFailure();
        $this->assertSame(CerberusInterface::CLOSED, $this->cerberus->getStatus());
        $this->cerberus->reportFailure();
        $this->cerberus->reportFailure();
        $this->assertSame(CerberusInterface::OPEN, $this->cerberus->getStatus());
        $this->assertSame(CerberusInterface::OPEN, $this->cerberus->getStatus());
    }

    public function testSuccess()
    {
        $this->cerberus->reportFailure();
        $this->assertSame(CerberusInterface::CLOSED, $this->cerberus->getStatus());
        $this->cerberus->reportSuccess();
        $this->cerberus->reportFailure();
        $this->assertSame(CerberusInterface::CLOSED, $this->cerberus->getStatus());
    }

    public function testHalfOpen()
    {
        $this->cerberus->reportFailure();
        $this->assertSame(CerberusInterface::CLOSED, $this->cerberus->getStatus());
        $this->cerberus->reportFailure();
        $this->cerberus->reportFailure();
        $this->assertSame(CerberusInterface::OPEN, $this->cerberus->getStatus());
        sleep(3);
        $this->assertSame(CerberusInterface::HALF_OPEN, $this->cerberus->getStatus());
    }
}
