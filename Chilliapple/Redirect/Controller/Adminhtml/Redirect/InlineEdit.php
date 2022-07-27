<?php
namespace Chilliapple\Redirect\Controller\Adminhtml\Redirect;

use Chilliapple\Redirect\Api\RedirectRepositoryInterface;
use Chilliapple\Redirect\Api\Data\RedirectInterface;
use Chilliapple\Redirect\Model\ResourceModel\Redirect as RedirectResourceModel;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Reflection\DataObjectProcessor;

/**
 * Class InlineEdit
 */
class InlineEdit extends Action
{
    /**
     * Redirect repository
     * @var RedirectRepositoryInterface
     */
    protected $redirectRepository;
    /**
     * Data object processor
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;
    /**
     * Data object helper
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;
    /**
     * JSON Factory
     * @var JsonFactory
     */
    protected $jsonFactory;
    /**
     * Redirect resource model
     * @var RedirectResourceModel
     */
    protected $redirectResourceModel;

    /**
     * constructor
     * @param Context $context
     * @param RedirectRepositoryInterface $redirectRepository
     * @param DataObjectProcessor $dataObjectProcessor
     * @param DataObjectHelper $dataObjectHelper
     * @param JsonFactory $jsonFactory
     * @param RedirectResourceModel $redirectResourceModel
     */
    public function __construct(
        Context $context,
        RedirectRepositoryInterface $redirectRepository,
        DataObjectProcessor $dataObjectProcessor,
        DataObjectHelper $dataObjectHelper,
        JsonFactory $jsonFactory,
        RedirectResourceModel $redirectResourceModel
    ) {
        $this->redirectRepository = $redirectRepository;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->jsonFactory = $jsonFactory;
        $this->redirectResourceModel = $redirectResourceModel;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        $postItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($postItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }

        foreach (array_keys($postItems) as $redirectId) {
            /** @var \Chilliapple\Redirect\Model\Redirect|\Chilliapple\Redirect\Api\Data\RedirectInterface $redirect */
            try {
                $redirect = $this->redirectRepository->get((int)$redirectId);
                $redirectData = $postItems[$redirectId];
                $this->dataObjectHelper->populateWithArray($redirect, $redirectData, RedirectInterface::class);
                $this->redirectResourceModel->saveAttribute($redirect, array_keys($redirectData));
            } catch (LocalizedException $e) {
                $messages[] = $this->getErrorWithRedirectId($redirect, $e->getMessage());
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithRedirectId($redirect, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithRedirectId(
                    $redirect,
                    __('Something went wrong while saving the Redirect.')
                );
                $error = true;
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }

    /**
     * Add Redirect id to error message
     *
     * @param \Chilliapple\Redirect\Api\Data\RedirectInterface $redirect
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithRedirectId(RedirectInterface $redirect, $errorText)
    {
        return '[Redirect ID: ' . $redirect->getId() . '] ' . $errorText;
    }
}
