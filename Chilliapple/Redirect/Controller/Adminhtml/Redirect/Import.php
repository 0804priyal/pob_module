<?php
namespace Chilliapple\Redirect\Controller\Adminhtml\Redirect;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Backend\App\Action;
use Magento\Framework\Registry;

class Import extends Action
{


    /**
     * create Redirect
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Chilliapple_Redirect::redirect_redirect');
        $resultPage->getConfig()->getTitle()->prepend(__('Redirects'));

        $resultPage->getConfig()->getTitle()->prepend(__('New Redirects Import'));
        return $resultPage;
    }
}
