<?php

namespace Training\Sales\Plugin;

class Product
{
    public function afterGetName(\Magento\Catalog\Model\Product $subject, $result)
    {
        return "Gate Software ".$result;
    }
}
