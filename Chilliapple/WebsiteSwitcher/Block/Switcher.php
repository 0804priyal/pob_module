<?php

namespace Chilliapple\WebsiteSwitcher\Block;

class Switcher extends \Magento\Framework\View\Element\Template
{

    public function getWebsites() {
        return $this->_storeManager->getWebsites();
    }

    public function getCurrentWebsiteId()
    {
        return $this->_storeManager->getWebsite()->getId();
    }

}
