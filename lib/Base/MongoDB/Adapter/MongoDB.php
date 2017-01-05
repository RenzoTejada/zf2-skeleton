<?php

namespace Base\MongoDB\Adapter;

use Zend\Db\Exception\ErrorException;
use \MongoClient;

class MongoDB extends MongoClient
{

    /**
     * @var array
     */
    protected $_options = array(
        "connect" => true,
        "connectTimeoutMS" => 5000,
    );

    /**
     * @var string
     */
    protected $_serverClass;

    /**
     * @var \MongoClient
     */
    protected $_connection;

    /**
     * @var array
     */
    protected $_config;

    /**
     * @var \MongoDB
     */
    protected $_db;

    /**
     *
     * @param array $config
     *
     * @return \Mongo
     */
    public function __construct(array $config)
    {
        $this->_checkRequiredOptions($config);

        $config['db'] = $config['dbname'];

        $this->_config = $config;

        foreach ($config as $key => $value) {
            if (empty($value)) {
                unset($config[$key]);
            }
        }

        $this->_options = array_merge($this->_options, $config);
        unset($this->_options['host'], $this->_options['port'],
            $this->_options['dbname']);

        parent::__construct('mongodb://' . $config['host'] . ':' . $config['port'],
            $this->_options);

        $this->setUpDatabase();
    }

    public function getDbName()
    {
        return $this->_config['db'];
    }

    public function getPassword()
    {
        return $this->_config['password'];
    }

    public function getUsername()
    {
        return $this->_config['username'];
    }

    public function getHost()
    {
        return $this->_config['host'];
    }

    public function getPort()
    {
        return $this->_config['port'];
    }

    public function setUpDatabase($db = null)
    {
        if ($db !== null) {
            $this->_config['db'] = $db;
        }

        $this->_db = $this->selectDB($this->getDbName());
    }

    public function getMongoDB()
    {
        return $this->_db;
    }

    public function query($query, $bind = array())
    {
        return $this->_db->execute($query);
    }

    public function __call($fn, $args)
    {
        if (empty($this->_db)) {
            throw new ErrorException("MongoDB Connection not initialized");
        }

        if (method_exists($this->_db, $fn)) {
            return call_user_func_array(array($this->_db, $fn), $args);
        }

        throw new ErrorException("MongoDB::{$fn} Method not found");
    }

    protected function _checkRequiredOptions(array $config)
    {
        if (!array_key_exists('dbname', $config)) {
            throw new ErrorException(
                "Configuration array must have a key for 'dbname' that names the database instance"
            );
        }
        if (!array_key_exists('password', $config)) {
            throw new ErrorException(
                "Configuration array must have a key for 'password' for login credentials"
            );
        }
        if (!array_key_exists('username', $config)) {
            throw new ErrorException(
                "Configuration array must have a key for 'username' for login credentials"
            );
        }
        if (!array_key_exists('host', $config)) {
            throw new ErrorException(
                "Configuration array must have a key for 'host'"
            );
        }
        if (!array_key_exists('port', $config)) {
            throw new ErrorException(
                "Configuration array must have a key for 'port'"
            );
        }
    }

    /** Abstract methods implementations * */

    /**
     * Returns a list of the collections in the database.
     * @return \MongoCursor Description
     */
    public function listTables()
    {
        return $this->_db->listCollections();
    }

    /**
     * @see self::listTables()
     */
    public function listCollections()
    {
        return $this->listTables();
    }

    /**
     * @todo improve
     */
    public function describeTable($tableName, $schemaName = null)
    {
        throw new ErrorException("Not implemented yet");
    }

    /**
     * {@inheritdoc}
     */
    protected function _connect()
    {
        if ($this->_options["connect"] == false) {
            $this->_connection->connect();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isConnected()
    {
        if(!$this->_connection){
            return;
        }
        return $this->_connection->connected;
    }

    /**
     * {@inheritdoc}
     */
    public function closeConnection()
    {
        return $this->_connection->close();
    }

    public function prepare($sql)
    {
        throw new ErrorException("Cannot prepare statements in MongoDB");
    }

    public function lastInsertId($tableName = null, $primaryKey = null)
    {
        throw new ErrorException("Not implemented yet");
    }

    protected function _beginTransaction()
    {
        throw new ErrorException("There are no transactions in MongoDB");
    }

    protected function _commit()
    {
        throw new ErrorException("There are no commits(ie: transactions) in MongoDB");
    }

    protected function _rollBack()
    {
        throw new ErrorException("There are no rollbacks(ie: transactions) in MongoDB");
    }

    /**
     * @todo improve
     */
    public function setFetchMode($mode)
    {
        throw new ErrorException("Not implemented yet");
    }

    /**
     * @todo improve
     */
    public function limit($sql, $count, $offset = 0)
    {
        throw new ErrorException("Not implemented yet");
    }

    /**
     * @todo improve
     */
    public function supportsParameters($type)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getServerVersion()
    {
        if ($this->_serverClass === '\MongoClient') {
            return \MongoClient::VERSION;
        }

        return \Mongo::VERSION;
    }

}
