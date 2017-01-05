<?php

namespace Base\MongoDB\Collection;

use Base\MongoDB\Collection\AdapterCollectionServiceAwareInterface;
use Base\MongoDB\Adapter\MongoDB as MongoAdapter;
use \MongoCollection;

abstract class BaseAdapterCollection extends MongoCollection implements AdapterCollectionServiceAwareInterface{
    
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     *
     * @var \MongoCollection
     */
    protected $collection;

    /**
     *
     * @var \MongoDB
     */
    protected $db;

    /**
     * @var bool
     */
    protected $isInitialized = false;
    
    public function __construct($collection) {
        if (!(is_string($collection))) {
            throw new Exception\InvalidArgumentException('Collection name must be a string or an instance of Collection');
        }
        $this->collection = $collection;
    }

    public function setAdapter(MongoAdapter $adapter){
       
        $this->db = $adapter->getMongoDB();
        parent::__construct($this->db, $this->collection); 
    }
     
    public function isInitialized()
    {
        return $this->isInitialized;
    }

    protected $keysAllowed = array();

    /**
     * Initialize
     *
     * @throws Exception\RuntimeException
     * @return null
     */
    public function initialize()
    {
        if ($this->isInitialized) {
            return;
        }

        if (!$this->db instanceof \MongoDB) {
            throw new Exception\RuntimeException('This collection does not have an MongoClient setup');
        }

        if (!is_string($this->collection) || $this->collection instanceof \MongoCollection) {
            throw new Exception\RuntimeException('This table object does not have a valid Collection set.');
        }

        $this->isInitialized = true;
    }

    /**
     * Get Collection name
     *
     * @return string
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * Get adapter
     *
     * @return \MongoDB
     */
    public function getDb()
    {
        return $this->db;
    }

    /**
     *
     * @param \MongoDB $db
     */
    public function setDb($db)
    {
        $this->setDb($db);
    }

    /**
     * Select
     *
     * @param array
     * @return array
     */
    public function select($where = null)
    {
        if (is_null($where)) {
            return iterator_to_array($this->find());
        }

        return iterator_to_array($this->find($where));
    }

    public function query($command, array $options = array())
    {
        return $this->getDb()->command($command, $options);
    }

    /**
     * Insert
     *
     * @param  array $set
     * @return int
     */
    /*public function save(array $array, $options = null)
    {
        $this->insert($array, $options);
    }*/

    public function delete($where = null, $options = null)
    {
        $this->remove($where, $options);
    }

    /**
     * Get last insert value
     *
     * @return int
     */
    public function getLastInsertValue()
    {
        return $this->lastInsertValue;
    }

    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    public function setServiceLocator(\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }


    public function _guardar($datos)
    {
        $id = 0;

        if (!empty($datos['_id'])) {
            $id = $datos['_id'];
        }
        unset($datos['_id']);

        //$datos = array_intersect_key($datos, array_flip($this->getCols()));
        $filter = new \Zend\Filter\StripTags();
        foreach ($datos as $key => $valor) {

            if($this->isKeyAllowed($key)){
                continue;
            }

            if (!is_array($valor) && !is_numeric($valor)) {
                //$datos[$key] = str_replace("'", '"', $valor);
                $datos[$key] = $filter->filter($valor);
            }
        }
        if ($id > 0) {
            $w = $this->update(
                        array('_id' => new \MongoId($id)),
                        array('$set' => $datos), array("w" => 1)
                );
            $id = ($w['n'] < 1) ? 0 : $id;
        } else {
            $this->insert($datos);
            $id = $datos['_id']->__toString();
        }

        return $id;
    }

    /**
     * Description
     *
     * @author Anderson
     * @param array
     * @return array
     */
    public function fetchPairs($where, $fields)
    {
        $cursor = $this->find($where, $fields)->sort(array($fields[1] => 1));
        $data = array();
        foreach ($cursor as $value) {
            if (!empty($value[$fields[1]])) {
                $data[(string)$value[$fields[0]]] = $value[$fields[1]];
            }
        }
        return $data;
    }

    protected function isKeyAllowed($key)
    {
        return in_array($key, $this->keysAllowed);
    }
    
}
