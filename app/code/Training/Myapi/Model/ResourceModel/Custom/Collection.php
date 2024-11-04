<?php

namespace Training\Myapi\Model\ResourceModel\Custom;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Training\Myapi\Model\Custom as Model;
use Training\Myapi\Model\ResourceModel\Custom as ResourceModel;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }
}
