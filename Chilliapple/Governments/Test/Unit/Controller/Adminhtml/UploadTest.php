<?php

declare(strict_types=1);

namespace Chilliapple\Governments\Test\Unit\Controller\Adminhtml;

use Chilliapple\Governments\Controller\Adminhtml\Upload;
use Chilliapple\Governments\Model\Uploader;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\Json;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UploadTest extends TestCase
{
    /**
     * @var Context | MockObject
     */
    private $context;
    /**
     * @var Uploader | MockObject
     */
    private $uploader;
    /**
     * @var ResultFactory | MockObject
     */
    private $resultFactory;
    /**
     * @var Json | MockObject
     */
    private $resultJson;
    /**
     * @var RequestInterface | MockObject
     */
    private $request;
    /**
     * @var Upload
     */
    private $upload;

    /**
     * setup tests
     */
    protected function setUp(): void
    {
        $this->context = $this->createMock(Context::class);
        $this->uploader = $this->createMock(Uploader::class);
        $this->request = $this->createMock(RequestInterface::class);
        $this->resultFactory = $this->createMock(ResultFactory::class);
        $this->resultJson = $this->createMock(Json::class);
        $this->resultFactory->method('create')->willReturn($this->resultJson);
        $this->context->method('getRequest')->willReturn($this->request);
        $this->context->method('getResultFactory')->willReturn($this->resultFactory);
        $this->upload = new Upload(
            $this->context,
            $this->uploader
        );
    }
    
    /**
     * @covers \Chilliapple\Governments\Controller\Adminhtml\Upload::execute
     * @covers \Chilliapple\Governments\Controller\Adminhtml\Upload::getFieldName
     * @covers \Chilliapple\Governments\Controller\Adminhtml\Upload::__construct
     */
    public function testExecute()
    {
        $this->uploader->method('saveFileToTmpDir')->willReturn(['result']);
        $this->resultJson->expects($this->once())->method('setData')->with(['result']);
        $this->assertEquals($this->resultJson, $this->upload->execute());
    }
    
    /**
     * @covers \Chilliapple\Governments\Controller\Adminhtml\Upload::execute
     * @covers \Chilliapple\Governments\Controller\Adminhtml\Upload::getFieldName
     * @covers \Chilliapple\Governments\Controller\Adminhtml\Upload::__construct
     */
    public function testExecuteWithException()
    {
        $this->uploader->method('saveFileToTmpDir')->willThrowException(new \Exception());
        $this->assertEquals($this->resultJson, $this->upload->execute());
    }
}
