<?php
namespace Chilliapple\Redirect\Controller\Adminhtml\Redirect;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Chilliapple\Redirect\Api\RedirectRepositoryInterface;

class Edit extends Action
{
    /**
     * @var RedirectRepositoryInterface
     */
    private $redirectRepository;
    /**
     * @var Registry
     */
    private $registry;

    /**
     * Edit constructor.
     * @param Context $context
     * @param RedirectRepositoryInterface $redirectRepository
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        RedirectRepositoryInterface $redirectRepository,
        Registry $registry
    ) {
        $this->redirectRepository = $redirectRepository;
        $this->registry = $registry;
        parent::__construct($context);
    }

    /**
     * get current Redirect
     *
     * @return null|\Chilliapple\Redirect\Api\Data\RedirectInterface
     */
    private function initRedirect()
    {
        $redirectId = $this->getRequest()->getParam('redirect_id');
        try {
            $redirect = $this->redirectRepository->get($redirectId);
        } catch (NoSuchEntityException $e) {
            $redirect = null;
        }
        $this->registry->register('current_redirect', $redirect);
        return $redirect;
    }

    /**
     * Edit or create Redirect
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $redirect = $this->initRedirect();
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Chilliapple_Redirect::redirect_redirect');
        $resultPage->getConfig()->getTitle()->prepend(__('Redirects'));

        if ($redirect === null) {
            $resultPage->getConfig()->getTitle()->prepend(__('New Redirect'));
        } else {
            $resultPage->getConfig()->getTitle()->prepend($redirect->getCode());
        }
        return $resultPage;
    }
}
