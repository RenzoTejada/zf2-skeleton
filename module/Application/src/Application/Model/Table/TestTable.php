<?php

namespace Application\Model\Table;

use Zend\Db\Sql\Sql;
use Base\Db\Table\AbstractTable;

class TestTable extends AbstractTable
{
    
    
    public function getTestAll()
    {
        $sql = new Sql($this->getAdapter());
        $select = $sql->select()
                ->from($this->table)
                ->limit(10);
        return $this->fetchAll($select);
    }
}

