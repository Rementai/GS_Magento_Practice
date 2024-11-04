<?php

namespace Training\Myapi\Model;

use Magento\Framework\Model\AbstractModel;
use Training\Myapi\Api\Data\CustomInterface;

class Custom extends AbstractModel implements CustomInterface
{
    protected function _construct()
    {
        $this->_init(ResourceModel\Custom::class);
    }

    public function getId()
    {
        return $this->_getData('id');
    }

    public function setId($id)
    {
        return $this->setData('id', $id);
    }

    public function getName()
    {
        return $this->_getData('name');
    }

    public function setName($name)
    {
        return $this->setData('name', $name);
    }

    public function getDescription()
    {
        return $this->_getData('description');
    }

    public function setDescription($description)
    {
        return $this->setData('description', $description);
    }
}
