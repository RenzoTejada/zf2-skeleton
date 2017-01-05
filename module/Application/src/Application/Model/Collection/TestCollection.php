<?php

namespace Application\Model\Collection;

use Base\MongoDB\Collection\Collection as BaseCollection;

class TestCollection extends BaseCollection
{

    public function getTest()
    {
        return $this->select();
    }
    
}
