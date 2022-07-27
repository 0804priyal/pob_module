<?php

declare(strict_types=1);

namespace Chilliapple\Governments\ViewModel\Government;

use Chilliapple\Governments\Api\Data\GovernmentInterface;
use Chilliapple\Governments\Api\GovernmentRepositoryInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class View implements ArgumentInterface
{
    /**
     * @var RequestInterface
     */
    private $request;
    /**
     * @var GovernmentRepositoryInterface
     */
    private $governmentRepository;
    /**
     * @var GovernmentInterface|bool
     */
    private $government;

    /**
     * View constructor.
     * @param RequestInterface $request
     * @param GovernmentRepositoryInterface $governmentRepository
     */
    public function __construct(RequestInterface $request, GovernmentRepositoryInterface $governmentRepository)
    {
        $this->request = $request;
        $this->governmentRepository = $governmentRepository;
    }

    /**
     * @return bool|GovernmentInterface
     */
    public function getGovernment()
    {
        if ($this->government === null) {
            $id = (int)$this->request->getParam('id');
            if ($id) {
                try {
                    $this->government = $this->governmentRepository->get($id);
                } catch (NoSuchEntityException $e) {
                    $this->government = false;
                }
            } else {
                $this->government = false;
            }
        }
        return $this->government;
    }
}
