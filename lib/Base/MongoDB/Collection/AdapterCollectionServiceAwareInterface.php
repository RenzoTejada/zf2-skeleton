<?php

namespace Base\MongoDB\Collection;
use Base\MongoDB\Adapter\MongoDB as MongoAdapter;

interface AdapterCollectionServiceAwareInterface {
    
    public function setAdapter(MongoAdapter $adapter);    
    
}

