<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        return new ViewModel();
    }
    
    public function modelAction()
    {
        $model = $this->getServiceLocator()->get('TestModel');
        $data = $model->getTestAll();
        return new ViewModel(array("data" => $data));
    }
    
    public function mongodbAction()
    {
        $Colection = $this->getServiceLocator()->get('TestCollection');
        $data = $Colection->getTest();
        return new ViewModel(array("data" => $data));
    }
}
