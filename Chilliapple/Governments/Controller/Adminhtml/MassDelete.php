<?php

declare(strict_types=1);

namespace Chilliapple\Governments\Controller\Adminhtml;

use Chilliapple\Governments\Ui\CollectionProviderInterface;
use Chilliapple\Governments\Ui\EntityUiConfig;
use Chilliapple\Governments\Ui\EntityUiManagerInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;

class MassDelete extends Action implements HttpPostActionInterface
{
    /**
     * @var Filter
     */
    private $filter;
    /**
     * @var CollectionProviderInterface
     */
    private $collectionProvider;
    /**
     * @var EntityUiConfig
     */
    private $uiConfig;
    /**
     * @var EntityUiManagerInterface
     */
    private $uiManager;

    /**
     * MassAction constructor.
     * @param Context $context
     * @param Filter $filter
     * @param CollectionProviderInterface $collectionProvider
     * @param EntityUiConfig $uiConfig
     * @param EntityUiManagerInterface $uiManager
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionProviderInterface $collectionProvider,
        EntityUiConfig $uiConfig,
        EntityUiManagerInterface $uiManager
    ) {
        $this->filter = $filter;
        $this->collectionProvider = $collectionProvider;
        $this->uiConfig = $uiConfig;
        $this->uiManager = $uiManager;
        parent::__construct($context);
    }

    /**
     * execute action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionProvider->getCollection());
            $collectionSize = $collection->getSize();
            foreach ($collection as $entity) {
                $this->uiManager->delete((int)$entity->getId());
            }
            $this->messageManager->addSuccessMessage(
                $this->uiConfig->getMassDeleteSuccessMessage($collectionSize)
            );
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($this->uiConfig->getMassDeleteErrorMessage());
        }
        /** @var \Magento\Framework\Controller\Result\Redirect $redirectResult */
        $redirectResult = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $redirectResult->setPath('*/*/index');
        return $redirectResult;
    }
}
