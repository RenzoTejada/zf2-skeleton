<?php

namespace Base\MongoDB;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Base\MongoDB\Adapter\MongoDB as BaseMongoDB;

class AdapterServiceFactory implements FactoryInterface
{

    /**
     * Create db adapter service
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return Adapter
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');

        return new BaseMongoDB($config['mongodb']['db']);
    }

}
