<?php

namespace Training\Myapi\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Custom extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('custom_table', 'id');
    }
}
