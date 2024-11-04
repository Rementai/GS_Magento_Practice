<?php
namespace Training\Sales\Block;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class Widget extends Template implements BlockInterface
{
    protected $_template = 'widget/helloworld.phtml';

    public function getTitle()
    {
        return $this->getData('title');
    }

    public function getMessage()
    {
        return $this->getData('message');
    }
}
