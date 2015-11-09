<?php

/**
 * Factory class for Cerberus.
 *
 * @author  Leandro Silva <leandro@leandrosilva.info>
 * @license https://github.com/mt-olympus/cerberus/blob/master/LICENSE MIT Licence
 */
namespace Cerberus;

use Zend\Cache\StorageFactory;
use Interop\Container\ContainerInterface;

/**
 * Factory class for Cerberus.
 *
 * @author  Leandro Silva <leandro@leandrosilva.info>
 * @license https://github.com/mt-olympus/cerberus/blob/master/LICENSE MIT Licence
 */
class Factory
{
    /**
     * Invoke method.
     *
     * @param \Interop\Container\ContainerInterface $container
     *
     * @return \Cerberus\Cerberus
     */
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->get('config');
        if (!isset($config['cerberus']['storage'])) {
            $storageConfig = [
                'adapter' => [
                    'name' => 'filesystem',
                    'cache_dir' => 'data/cache',
                ],
                'plugins' => [
                    // Don't throw exceptions on cache errors
                    'exception_handler' => [
                        'throw_exceptions' => false,
                    ],
                    'Serializer',
                ],
            ];
        } else {
            $storageConfig = $config['cerberus']['storage'];
        }

        $storage = StorageFactory::factory($storageConfig);

        return new Cerberus($storage);
    }
}
