<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Db\Adapter\Adapter as DbAdapter;
use Application\Model\Table;
use Application\Model\Collection;
use Base\MongoDB\Adapter\MongoDB as BaseMongoDB;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'DbAdapter' => function ($sm) {
                    $config = $sm->get('config');
                    $config = $config['db'];
                    $dbAdapter = new DbAdapter($config);

                    return $dbAdapter;
                },
                'MongoClient' => function ($sm) {
                    $config = $sm->get('config');
                    $config = $config['mongodb']['db'];

                    return new BaseMongoDB($config);
                },
                'TestCollection' => function ($sm) {
                    $mongoDb = $sm->get('MongoClient')->getMongoDB();

                    return new Collection\TestCollection(
                            'test', $mongoDb
                    );
                },
                'TestTable' => function ($sm) {
                    $TestTable = new Table\TestTable('test', $sm->get('DbAdapter'));
                    return $TestTable;
                },
            ),
            'aliases' => array(
                'TestModel' => 'TestTable',
            ),
        );
    }
}
