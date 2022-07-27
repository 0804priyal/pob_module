<?php
namespace Chilliapple\Redirect\Model\Redirect\Executor;

use Chilliapple\Redirect\Api\RedirectRepositoryInterface;
use Chilliapple\Redirect\Api\ExecutorInterface;

class Delete implements ExecutorInterface
{
    /**
     * @var RedirectRepositoryInterface
     */
    private $redirectRepository;

    /**
     * Delete constructor.
     * @param RedirectRepositoryInterface $redirectRepository
     */
    public function __construct(
        RedirectRepositoryInterface $redirectRepository
    ) {
        $this->redirectRepository = $redirectRepository;
    }

    /**
     * @param int $id
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute($id)
    {
        $this->redirectRepository->deleteById($id);
    }
}
