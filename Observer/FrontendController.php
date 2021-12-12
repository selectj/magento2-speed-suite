<?php

namespace Selectj\SpeedSuite\Observer;

use Magento\Framework\Event\ObserverInterface;
use Selectj\SpeedSuite\Helper\Data;


class Observer implements ObserverInterface
{
    protected $_helper;

    public function __construct(
        Data $helper
    )
    {
        $this->_helper = $helper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->_helper->isDeferJsEnabled())
            return;

        $response = $observer->getEvent()->getData('response');
        if (!$response)
            return;
        $html = $response->getBody();
        if ($html == '')
            return;
        $conditionalJsPattern = '@(?:<script type="text/javascript"|<script)(.*)</script>@msU';
        preg_match_all($conditionalJsPattern, $html, $_matches);
        $_js_if = implode('', $_matches[0]);
        $html = preg_replace($conditionalJsPattern, '', $html);
        $html .= $_js_if;
        $response->setBody($html);
    }
}