<?php

namespace Base\Db\Table;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Metadata\Metadata;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Cache\Storage\StorageInterface;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Db\ResultSet\ResultSet;
use Base\Db\Table\AdapterDbServiceAwareInterface;

/**
 * @todo upgrade a model
 */
class AbstractTable extends TableGateway implements AdapterDbServiceAwareInterface//ServiceLocatorAwareInterface
{

    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;
    protected $cache;

    public function fetchAll($select)
    {
        $sql = new Sql($this->getAdapter());
        $selectString = $sql->prepareStatementForSqlObject($select);
        $result = $selectString->execute();
        $resultSet = new ResultSet();
        return $resultSet->initialize($result)->toArray();

    }

    public function fetchRow($select)
    {
        $sql = new Sql($this->getAdapter());
        $selectString = $sql->prepareStatementForSqlObject($select);
        return $selectString->execute()->current();
    }

    public function fetchAssoc($select)
    {
        $sql = new Sql($this->getAdapter());
        $selectString = $sql->prepareStatementForSqlObject($select);
        $result = $selectString->execute();
        $resultSet = new ResultSet();
        $stmt = $resultSet->initialize($result)->toArray();
        $data = array();
        foreach ($stmt as $row) {
            $tmp = array_values(array_slice($row, 0, 1));
            $data[$tmp[0]] = $row;
        }

        return $data;
    }

    public function fetchCol($select)
    {
        $sql = new Sql($this->getAdapter());
        $selectString = $sql->prepareStatementForSqlObject($select);
        $result = $selectString->execute();
        $resultSet = new ResultSet();
        $stmt = $resultSet->initialize($result)->toArray();
        $data = array();
        foreach ($stmt as $row) {
            $tmp = array_values(array_slice($row, 0, 1));
            $data[] = $tmp[0];
        }

        return $data;
    }

    public function fetchPairs($select)
    {
        $sql = new Sql($this->getAdapter());
        $selectString = $sql->prepareStatementForSqlObject($select);
        $result = $selectString->execute();
        $resultSet = new ResultSet();
        $stmt = $resultSet->initialize($result)->toArray();
        $data = array();
        foreach ($stmt as $row) {
            $tmp = array_values(array_slice($row, 0, 2));
            $data[$tmp[0]] = $tmp[1];
        }

        return $data;
    }

    public function fetchOne($select)
    {
        $sql = new Sql($this->getAdapter());
        $selectString = $sql->prepareStatementForSqlObject($select);
        $stmt = $selectString->execute()->current();
        if (empty($stmt))
            return 0;
        $tmp = array_values(array_slice($stmt, 0, 1));
        $result = $tmp[0];

        return $result;
    }

    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    public function getCols()
    {
        $metadata = new Metadata($this->getAdapter());

        return $metadata->getColumnNames($this->getTable());
    }

    public function _guardar($datos)
    {
        $id = 0;

        if (!empty($datos['id'])) {
            $id = (int) $datos['id'];
        }
        unset($datos['id']);

        $datos = array_intersect_key($datos, array_flip($this->getCols()));

        $filter = new \Zend\Filter\StripTags(
                array(
                    'allowTags' => array('a','br'),
                    'allowAttribs' => 'href'
                    )
                );
        foreach ($datos as $key => $valor) {
            if (!is_array($valor)&&!is_numeric($valor))
                $datos[$key] = $filter->filter($valor);
        }

        if ($id > 0) {
            $cantidad = $this->update($datos, 'id = ' . $id);
            $id = ($cantidad < 1) ? 0 : $id;
        } else {
            $this->insert($datos);
            $id = $this->lastInsertValue;
        }

        return $id;
    }

    public function query($select)
    {
        return $this->getAdapter()->query($select, Adapter::QUERY_MODE_EXECUTE);
    }

    public function _guardarGenerico($datos, $clave = 'id', $action = 0)
    {
        $id = 0;

        if (!empty($datos[$clave])) {
            $id = (int) $datos[$clave];
        }
        unset($datos[$clave]);

        $datos = array_intersect_key($datos, array_flip($this->getCols()));

        foreach ($datos as $key => $valor) {
            if (!is_numeric($valor)) {
                $datos[$key] = str_replace("'", '"', $valor);
            }
        }
        if ($action > 0) {
            $cantidad = $this->update($datos, "$clave = " . $id);
            $id = ($cantidad < 1) ? 0 : $id;
        } else {
            $datos[$clave] = $id;
            $this->insert($datos);
            $id = $this->lastInsertValue;
        }

        return $id;
    }

    public function getEvent()
    {
        return $this->getServiceLocator()->get('Application')->getEventManager();
    }

    public function getConfig()
    {
        return $this->getServiceLocator()->get('config');
    }

    public function setCache(StorageInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * To easily get a paginator instance for a select object
     * @author Anderson
     * @param $select \Zend\Db\Sql\Select The select object to paginate
     * @param $page int The current page number
     * @param $limit int Number of items per page
     * @return \Zend\Paginator\Paginator
     */
    protected function getPaginatorForSelect($select, $page, $limit = 20,
            $pageRange = 10)
    {
        //-- Utilizar Buffer para Paginator
        $resulset = new \Zend\Db\ResultSet\ResultSet();
        $resulset->buffer();
        $paginatorAdapter = new DbSelect($select, $this->getAdapter(), $resulset);
        $paginator = new Paginator($paginatorAdapter);

        $paginator->setItemCountPerPage($limit);
        $paginator->setPageRange($pageRange);
        $paginator->setCurrentPageNumber($page);

        return $paginator;
    }

    public function setAdapter(Adapter $adapter) {
        $this->adapter = $adapter;
        $this->sql = new Sql($adapter);
    }

}
