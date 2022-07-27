<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Reports
 */


namespace Amasty\Reports\Block\Adminhtml;

use Amasty\Reports\Api\Data\RuleInterface;
use Amasty\Reports\Api\RuleRepositoryInterface;
use Magento\Backend\Block\Template;
use Magento\Framework\Exception\NoSuchEntityException;

class Navigation extends Template
{
    /**
     * @var string
     */
    public $title;

    /**
     * @var RuleRepositoryInterface
     */
    private $ruleRepository;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    private $moduleManager;

    public function __construct(
        RuleRepositoryInterface $ruleRepository,
        Template\Context $context,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->ruleRepository = $ruleRepository;
        $this->moduleManager = $moduleManager;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return [
            'dashboard' => [
                'title'     => __('Dashboard'),
                'url'       => 'amasty_reports/report',
                'children'  => [],
                'resource'  => 'Amasty_Reports::reports'
            ],
            'sales' => [
                'title'     => __('Sales and Orders'),
                'resource'  => 'Amasty_Reports::reports_sales',
                'children'  => [
                    'overview' => [
                        'title' => __('Overview'),
                        'url' => 'amasty_reports/report_sales/overview',
                        'resource'  => 'Amasty_Reports::reports_sales_overview'
                    ],
                    'overview_compare' => [
                        'title' => __('Sales Comparison'),
                        'url' => 'amasty_reports/report_sales/compare',
                        'resource'  => 'Amasty_Reports::reports_sales_compare'
                    ],
                    'orders' => [
                        'title' => __('Orders'),
                        'url' => 'amasty_reports/report_sales/orders',
                        'resource'  => 'Amasty_Reports::reports_sales_orders'
                    ],
                    'order_items' => [
                        'title' => __('Order Items'),
                        'url' => 'amasty_reports/report_sales/orderItems',
                        'resource'  => 'Amasty_Reports::reports_sales_order_items'
                    ],
                ]
            ],
            'sales_by' => [
                'title'     => __('Sales By'),
                'resource'  => 'Amasty_Reports::reports_sales_by',
                'children'  => [
                    'by_hour' => [
                        'title' => __('Hour'),
                        'url' => 'amasty_reports/report_sales/hour',
                        'resource'  => 'Amasty_Reports::reports_sales_hour'
                    ],
                    'by_day' => [
                        'title' => __('Week'),
                        'url' => 'amasty_reports/report_sales/weekday',
                        'resource'  => 'Amasty_Reports::reports_sales_weekday'
                    ],
                    'by_country' => [
                        'title' => __('Country'),
                        'url' => 'amasty_reports/report_sales/country',
                        'resource'  => 'Amasty_Reports::reports_sales_country'
                    ],
                    'by_country_state' => [
                        'title' => __('Country - State'),
                        'url' => 'amasty_reports/report_sales/state',
                        'resource'  => 'Amasty_Reports::reports_sales_state'
                    ],
                    'by_payment' => [
                        'title' => __('Payment Type'),
                        'url' => 'amasty_reports/report_sales/payment',
                        'resource'  => 'Amasty_Reports::reports_sales_payment'
                    ],
                    'by_group' => [
                        'title' => __('Customer Group'),
                        'url' => 'amasty_reports/report_sales/group',
                        'resource'  => 'Amasty_Reports::reports_sales_group'
                    ],
                    'by_coupon' => [
                        'title' => __('Coupon'),
                        'url' => 'amasty_reports/report_sales/coupon',
                        'resource'  => 'Amasty_Reports::reports_sales_coupon'
                    ],
                    'by_category' => [
                        'title' => __('Category'),
                        'url' => 'amasty_reports/report_sales/category',
                        'resource'  => 'Amasty_Reports::reports_sales_category'
                    ],
                    'by_cart_price_rule' => [
                        'title' => __('Cart Price Rules'),
                        'url' => 'amasty_reports/report_sales/cartRule',
                        'resource'  => 'Amasty_Reports::reports_sales_cartRule'
                    ],
                ],
            ],
            'catalog' => [
                'title'     => __('Catalog'),
                'resource'  => 'Amasty_Reports::reports_catalog',
                'children'  => [
                    'by_product' => [
                        'title' => __('By Product'),
                        'url' => 'amasty_reports/report_catalog/byProduct',
                        'resource'  => 'Amasty_Reports::reports_catalog_by_product'
                    ],
                    'by_attributes' => [
                        'title' => __('By Product Attributes'),
                        'url' => 'amasty_reports/report_catalog/byAttributes',
                        'resource'  => 'Amasty_Reports::reports_catalog_by_attributes'
                    ],
                    'by_brands' => [
                        'title' => __('By Brands'),
                        'url' => 'amasty_reports/report_catalog/byBrands',
                        'resource'  => 'Amasty_Reports::reports_catalog_by_brands'
                    ],
                    'product_performance' => [
                        'title' => __('Product Performance'),
                        'url' => 'amasty_reports/report_catalog/productPerformance',
                        'resource'  => 'Amasty_Reports::reports_catalog_product_performance'
                    ],
                    'bestsellers' => [
                        'title' => __('Bestsellers'),
                        'url' => 'amasty_reports/report_catalog/bestsellers',
                        'resource'  => 'Amasty_Reports::reports_catalog_bestsellers'
                    ],
                ]
            ],
            'customers' => [
                'title'     => __('Customers'),
                'resource'  => 'Amasty_Reports::reports_customers',
                'children'  => [
                    'customers' => [
                        'title' => __('Customers'),
                        'url' => 'amasty_reports/report_customers/customers',
                        'resource'  => 'Amasty_Reports::reports_customers_customers'
                    ],
                    'returning' => [
                        'title' => __('New vs Returning Customers'),
                        'url' => 'amasty_reports/report_customers/returning',
                        'resource'  => 'Amasty_Reports::reports_customers_returning'
                    ],
                    'abandoned' => [
                        'title' => __('Abandoned Carts'),
                        'url' => 'amasty_reports/report_customers/abandoned',
                        'resource'  => 'Amasty_Reports::reports_customers_abandoned'
                    ],
                    'conversion_rate' => [
                        'title' => __('Conversion Rate'),
                        'url' => 'amasty_reports/report_customers/conversionRate',
                        'resource'  => 'Amasty_Reports::reports_customers_conversion_rate'
                    ],
                ]
            ],
            'custom' => [
                'title' => __('Custom Reports'),
                'resource' => 'Amasty_Reports::reports_catalog_by_product',
                'class' => 'amreports-newrule-container',
                'children' => $this->getCustomReports()
            ],
            'more_analytics' => [
                'title'     => __('More Analytics'),
                'resource'  => 'Amasty_Reports::reports',
                'children'  => [
                    'advanced_search' => [
                        'title' => __('Advanced Search'),
                        'url' => 'amsearch/report/index',
                        'class' => 'amreports-more-analytic',
                        'isInstalled' => $this->moduleManager->isEnabled('Amasty_Xsearch'),
                        'resource'  => 'Amasty_Reports::reports',
                        'tooltip_description'  => __('Stay ahead of competitors by understanding the demand.'),
                        'img' => $this->_assetRepo->getUrl('Amasty_Reports::img/more-analytic/advanced-search.png'),
                        'adv_url' => 'https://amasty.com/advanced-search-for-magento-2.html'
                            . '?utm_source=demo&utm_medium=gotopage&utm_campaign=advanced-search_m2',
                        'adv_m_url' => 'https://marketplace.magento.com/amasty-xsearch.html',
                    ],
                    'out_of_stock_notification' => [
                        'title' => __('Out Of Stock Notification'),
                        'url' => 'xnotif/analytics/index',
                        'class' => 'amreports-more-analytic',
                        'isInstalled' => $this->moduleManager->isEnabled('Amasty_Xnotif'),
                        'resource'  => 'Amasty_Reports::reports',
                        'tooltip_description'  => __('Stay ahead of competitors by understanding the demand.'),
                        'img' => $this->_assetRepo->getUrl('Amasty_Reports::img/more-analytic/osn.png'),
                        'adv_url' => 'https://amasty.com/out-of-stock-notification-for-magento-2.html'
                            . '?utm_source=demo&utm_medium=gotopage&utm_campaign=out-of-stock-notification_m2',
                        'adv_m_url' => 'https://marketplace.magento.com/amasty-xnotif.html',
                    ],
                    'one_step_checkout' => [
                        'title' => __('One Step Checkout'),
                        'url' => 'amasty_checkout/reports/',
                        'class' => 'amreports-more-analytic',
                        'isInstalled' => $this->moduleManager->isEnabled('Amasty_Checkout'),
                        'resource'  => 'Amasty_Reports::reports',
                        'tooltip_description'  => __('Stay ahead of competitors by understanding the demand.'),
                        'img' => $this->_assetRepo->getUrl('Amasty_Reports::img/more-analytic/one-step-checkout.png'),
                        'adv_url' => 'https://amasty.com/one-step-checkout-for-magento-2.html'
                            . '?utm_source=demo&utm_medium=gotopage&utm_campaign=one-step-checkout_m2',
                        'adv_m_url' => 'https://marketplace.magento.com/amasty-module-single-step-checkout.html',
                    ],
                    'social_login' => [
                        'title' => __('Social Login'),
                        'url' => 'customer/index/',
                        'class' => 'amreports-more-analytic',
                        'isInstalled' => $this->moduleManager->isEnabled('Amasty_SocialLogin'),
                        'resource'  => 'Amasty_Reports::reports',
                        'tooltip_description'  => __('Stay ahead of competitors by understanding the demand.'),
                        'img' => $this->_assetRepo->getUrl('Amasty_Reports::img/more-analytic/social-login.png'),
                        'adv_url' => 'http://amasty.com/social-login-for-magento-2.html'
                            . '?utm_source=demo&utm_medium=gotopage&utm_campaign=social-login_m2',
                        'adv_m_url' => 'https://marketplace.magento.com/amasty-social-login-meta.html',
                    ],
                ]
            ],
        ];
    }

    /**
     * @return array
     */
    private function getCustomReports()
    {
        $result = [];
        try {
            /** @var RuleInterface $rule */
            foreach ($this->ruleRepository->getPinnedRules()->getItems() as $rule) {
                $result[] = [
                    'title' => $rule->getTitle(),
                    'resource' => 'Amasty_Reports::reports_catalog_by_product',
                    'url' => $this->_urlBuilder->getUrl('amasty_reports/report_catalog/byProduct')
                        . '?amreports[rule]=' . $rule->getEntityId()
                ];
            }
        } catch (NoSuchEntityException $e) {
            $this->_logger->debug($e->getMessage());
        }
        $result[] = [
            'title' => '+ ' . __('New Rule'),
            'resource' => 'Amasty_Reports::reports_catalog_by_product',
            'class' => 'amreports-newrule',
            'url' => $this->_urlBuilder->getUrl('amasty_reports/rule')
        ];

        return $result;
    }

    /**
     * @return string
     */
    public function getCurrentTitle()
    {
        if (!$this->title) {
            $config = $this->getConfig();
            foreach ($config as $groupKey => &$group) {
                foreach ($group['children'] as $childKey => &$child) {
                    if (isset($child['url']) && $this->isUrlActive($child['url'])) {
                        $this->title = $child['title'];
                    }
                }
            }
        }

        return $this->title;
    }

    /**
     * @return mixed
     */
    public function getMenu()
    {
        $config = $this->getMenuConfig();

        foreach ($config as $groupKey => &$group) {
            if (isset($group['resource']) && !$this->_authorization->isAllowed($group['resource'])) {
                unset($config[$groupKey]);
                continue;
            }

            if (isset($group['url']) && $this->isUrlActive($group['url'])) {
                $group['active'] = true;
            }

            foreach ($group['children'] as $childKey => &$child) {
                if (isset($child['resource']) && !$this->_authorization->isAllowed($child['resource'])) {
                    unset($group['children'][$childKey]);
                    continue;
                }

                if (isset($child['url']) && $this->isUrlActive($child['url'])) {
                    $this->title = $child['title'];
                    $child['active'] = true;
                    $group['active'] = true;
                }
            } unset($child);
        }

        return $config;
    }

    /**
     * @param $url
     * @return bool
     */
    protected function isUrlActive($url)
    {
        $url = $this->normalizeUrl($url);

        return (false !== strpos($this->getRequest()->getPathInfo(), "/$url/"));
    }

    /**
     * @param $url
     * @return string
     */
    protected function normalizeUrl($url)
    {
        $parts = explode('/', $url);

        while (count($parts) < 3) {
            $parts []= 'index';
        }

        return implode('/', $parts);
    }
}
