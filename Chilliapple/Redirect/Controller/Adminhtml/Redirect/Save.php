<?php
namespace Chilliapple\Redirect\Controller\Adminhtml\Redirect;

use Chilliapple\Redirect\Api\RedirectRepositoryInterface;
use Chilliapple\Redirect\Api\Data\RedirectInterface;
use Chilliapple\Redirect\Api\Data\RedirectInterfaceFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Registry;
use Chilliapple\Redirect\Model\UploaderPool;

/**
 * Class Save
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Save extends Action
{
    /**
     * Redirect factory
     * @var RedirectInterfaceFactory
     */
    protected $redirectFactory;
    /**
     * Data Object Processor
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;
    /**
     * Data Object Helper
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;
    /**
     * Data Persistor
     * @var DataPersistorInterface
     */
    protected $dataPersistor;
    /**
     * Uploader pool
     * @var UploaderPool
     */
    protected $uploaderPool;
    /**
     * Core registry
     * @var Registry
     */
    protected $registry;
    /**
     * Redirect repository
     * @var RedirectRepositoryInterface
     */
    protected $redirectRepository;

    /**
     * Save constructor.
     * @param Context $context
     * @param RedirectInterfaceFactory $redirectFactory
     * @param RedirectRepositoryInterface $redirectRepository
     * @param DataObjectProcessor $dataObjectProcessor
     * @param DataObjectHelper $dataObjectHelper
     * @param DataPersistorInterface $dataPersistor
     * @param UploaderPool $uploaderPool
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        RedirectInterfaceFactory $redirectFactory,
        RedirectRepositoryInterface $redirectRepository,
        DataObjectProcessor $dataObjectProcessor,
        DataObjectHelper $dataObjectHelper,
        DataPersistorInterface $dataPersistor,
        UploaderPool $uploaderPool,
        Registry $registry
    ) {
        $this->redirectFactory = $redirectFactory;
        $this->redirectRepository = $redirectRepository;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataPersistor = $dataPersistor;
        $this->registry = $registry;
        $this->uploaderPool = $uploaderPool;
        parent::__construct($context);
    }

    /**
     * run the action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        /** @var RedirectInterface $redirect */
        $redirect = null;
        $postData = $this->getRequest()->getPostValue();
        $data = $postData;
        $id = !empty($data['redirect_id']) ? $data['redirect_id'] : null;
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            if ($id) {
                $redirect = $this->redirectRepository->get((int)$id);
            } else {
                unset($data['redirect_id']);
                $redirect = $this->redirectFactory->create();
            }
            $file = $this->uploaderPool->getUploader('file')->uploadFileAndGetName('file', $data);
            $data['file'] = $file;
            $this->dataObjectHelper->populateWithArray($redirect, $data, RedirectInterface::class);
            $this->redirectRepository->save($redirect);
            $this->messageManager->addSuccessMessage(__('You saved the Redirect'));
            $this->dataPersistor->clear('chilliapple_redirect_redirect');
            if ($this->getRequest()->getParam('back')) {
                $resultRedirect->setPath('*/*/edit', ['redirect_id' => $redirect->getId()]);
            } else {
                $resultRedirect->setPath('*/*');
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->dataPersistor->set('chilliapple_redirect_redirect', $postData);
            $resultRedirect->setPath('*/*/edit', ['redirect_id' => $id]);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('There was a problem saving the Redirect'));
            $this->dataPersistor->set('chilliapple\redirect_redirect', $postData);
            $resultRedirect->setPath('*/*/edit', ['redirect_id' => $id]);
        }
        return $resultRedirect;
    }
}
