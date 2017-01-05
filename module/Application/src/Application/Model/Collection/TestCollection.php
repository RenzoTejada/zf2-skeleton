<?php

namespace Application\Model\Collection;

use Base\MongoDB\Collection\Collection as BaseCollection;

class TestCollection extends BaseCollection
{

    public function getTest()
    {
        $result = iterator_to_array($this->find(), false);
        return $result;
    }
    
}
