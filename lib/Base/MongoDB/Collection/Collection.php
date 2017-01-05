<?php

namespace Base\MongoDB\Collection;

class Collection extends AbstractCollection
{
    protected $connection;

    public function __construct($collection, $db)
    {
        if (!(is_string($collection))) {
            throw new Exception\InvalidArgumentException('Collection name must be a string or an instance of Collection');
        }
        $this->collection = $collection;

        $this->db = $db;
        $this->initialize();
        parent::__construct($this->db, $this->collection);
    }

}
