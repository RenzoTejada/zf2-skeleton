<?php

namespace Base\Db\Table;

use Zend\Db\Adapter\Adapter;

interface AdapterDbServiceAwareInterface {

    public function setAdapter(Adapter $adapter);
}
