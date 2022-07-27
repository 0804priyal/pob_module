<?php

declare(strict_types=1);

namespace Chilliapple\Governments\Test\Unit\Controller\Government;

use Chilliapple\Governments\Api\GovernmentRepositoryInterface;
use Chilliapple\Governments\Controller\Government\View;
use Chilliapple\Governments\Model\Government;
use Chilliapple\Governments\ViewModel\Government\Url;
use Magento\Backend\Model\View\Result\Forward;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\View\Page\Config;
use Magento\Framework\View\Page\Title;
use Magento\Framework\View\Result\Page;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Theme\Block\Html\Breadcrumbs;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ViewTest extends TestCase
{
    /**
     * @var Context | MockObject
     */
    private $context;
    /**
     * @var GovernmentRepositoryInterface | MockObject
     */
    private $governmentRepository;
    /**
     * @var Url | MockObject
     */
    private $urlModel;
    /**
     * @var ScopeConfigInterface | MockObject
     */
    private $scopeConfig;
    /**
     * @var RequestInterface | MockObject
     */
    private $request;
    /**
     * @var ResultFactory | MockObject
     */
    private $resultFactory;
    /**
     * @var Config | MockObject
     */
    private $pageConfig;
    /**
     * @var Title | MockObject
     */
    private $pageTitle;
    /**
     * @var LayoutInterface | MockObject
     */
    private $layout;
    /**
     * @var UrlInterface | MockObject
     */
    private $url;
    /**
     * @var Page | MockObject
     */
    private $page;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var View
     */
    private $view;

    /**
     * setup tests
     */
    protected function setUp(): void
    {
        $this->context = $this->createMock(Context::class);
        $this->governmentRepository = $this->createMock(GovernmentRepositoryInterface::class);
        $this->urlModel = $this->createMock(Url::class);
        $this->storeManager = $this->createMock(StoreManagerInterface::class);
        $store = $this->createMock(StoreInterface::class);
        $store->method('getId')->willReturn(1);
        $this->storeManager->method('getStore')->willReturn($store);
        $this->scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $this->request = $this->createMock(RequestInterface::class);
        $this->context->method('getRequest')->willReturn($this->request);
        $this->layout = $this->createMock(LayoutInterface::class);
        $this->page = $this->createMock(Page::class);
        $this->resultFactory = $this->createMock(ResultFactory::class);
        $this->pageConfig = $this->createMock(Config::class);
        $this->page->method('getConfig')->willReturn($this->pageConfig);
        $this->pageTitle = $this->createMock(Title::class);
        $this->pageConfig->method('getTitle')->willReturn($this->pageTitle);
        $this->page->method('getLayout')->willReturn($this->layout);
        $this->url = $this->createMock(UrlInterface::class);
        $this->context->method('getUrl')->willReturn($this->url);
        $this->context->method('getResultFactory')->willReturn($this->resultFactory);

        $this->view = new View(
            $this->context,
            $this->governmentRepository,
            $this->urlModel,
            $this->storeManager,
            $this->scopeConfig
        );
    }

    /**
     * @covers \Chilliapple\Governments\Controller\Government\View::execute
     * @covers \Chilliapple\Governments\Controller\Government\View::__construct
     */
    public function testExecute()
    {
        $this->request->method('getParam')->willReturn(1);
        $this->governmentRepository->method('get')->willReturn($this->getGovernmentMock(true, [0]));
        $this->resultFactory->method('create')->willReturn($this->page);
        $this->scopeConfig->method('isSetFlag')->willReturn(true);
        $crumbs = $this->createMock(Breadcrumbs::class);
        $titleBlock = $this->createMock(\Magento\Theme\Block\Html\Title::class);
        $this->layout->expects($this->exactly(2))->method('getBlock')->willReturnMap([
            ['page.main.title' , $titleBlock],
            ['breadcrumbs', $crumbs]
        ]);
        $crumbs->expects($this->exactly(3))->method('addCrumb');
        $this->assertEquals($this->page, $this->view->execute());
    }

    /**
     * @covers \Chilliapple\Governments\Controller\Government\View::execute
     * @covers \Chilliapple\Governments\Controller\Government\View::getNoRouteResult
     * @covers \Chilliapple\Governments\Controller\Government\View::__construct
     */
    public function testExecuteDisabled()
    {
        $this->request->method('getParam')->willReturn(1);
        $this->governmentRepository->method('get')->willReturn($this->getGovernmentMock(false, [0]));
        $forward = $this->createMock(Forward::class);
        $this->resultFactory->method('create')->willReturn($forward);
        $forward->expects($this->once())->method('forward');
        $this->scopeConfig->expects($this->never())->method('isSetFlag');
        $this->assertEquals($forward, $this->view->execute());
    }

    /**
     * @covers \Chilliapple\Governments\Controller\Government\View::execute
     * @covers \Chilliapple\Governments\Controller\Government\View::__construct
     */
    public function testExecuteNoBreadcrumbs()
    {
        $this->request->method('getParam')->willReturn(1);
        $this->governmentRepository->method('get')->willReturn($this->getGovernmentMock(true, [0]));
        $this->resultFactory->method('create')->willReturn($this->page);
        $this->scopeConfig->method('isSetFlag')->willReturn(false);
        $titleBlock = $this->createMock(\Magento\Theme\Block\Html\Title::class);
        $this->layout->expects($this->once())->method('getBlock')->willReturn($titleBlock);
        $this->assertEquals($this->page, $this->view->execute());
    }

    /**
     * @covers \Chilliapple\Governments\Controller\Government\View::execute
     * @covers \Chilliapple\Governments\Controller\Government\View::getNoRouteResult
     * @covers \Chilliapple\Governments\Controller\Government\View::__construct
     */
    public function testExecuteWithException()
    {
        $this->request->method('getParam')->willReturn(1);
        $this->governmentRepository->method('get')->willThrowException(
            $this->createMock(NoSuchEntityException::class)
        );
        $forward = $this->createMock(Forward::class);
        $this->resultFactory->method('create')->willReturn($forward);
        $forward->expects($this->once())->method('forward');
        $this->scopeConfig->expects($this->never())->method('isSetFlag');
        $this->assertEquals($forward, $this->view->execute());
    }
    /**
     * @param $isActive
     * @param $storeIds
     * @return MockObject
     * @throws \ReflectionException
     */
    private function getGovernmentMock($isActive, $storeIds)
    {
        $mock = $this->createMock(Government::class);
        $mock->method('getIsActive')->willReturn($isActive);
        $mock->method('getStoreId')->willReturn($storeIds);
        return $mock;
    }
}
