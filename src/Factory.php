<?php
namespace Cerberus;

use Zend\Cache\StorageFactory;
use Interop\Container\ContainerInterface;

class Factory
{
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->get('config');
        if (!isset($config['cerberus']['storage'])) {
            $storageConfig = [
                'adapter' => [
                    'name'    => 'filesystem',
                    'cache_dir' => 'data/cache',
                ],
                'plugins' => [
                    // Don't throw exceptions on cache errors
                    'exception_handler' => [
                        'throw_exceptions' => false,
                    ],
                    'Serializer',
                ]
            ];
        } else {
            $storageConfig = $config['cerberus']['storage'];
        }

        $storage = StorageFactory::factory($storageConfig);
        return new Cerberus($storage);
    }
}