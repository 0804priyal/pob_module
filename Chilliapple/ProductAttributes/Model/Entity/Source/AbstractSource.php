<?php

namespace Chilliapple\ProductAttributes\Model\Entity\Source;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Data\OptionSourceInterface;


class AbstractSource extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    protected $configField = null;

    protected $helper;
    /**
     * @var CategoryRepositoryInterface
     */
    private $repository;
    /**
     * @var array
     */
    private $options;

    /**
     * CoinYear constructor.
     * @param CategoryRepositoryInterface $repository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        \Magento\Catalog\Model\CategoryRepository $repository,
        \Chilliapple\ProductAttributes\Helper\Data $helper
    ) {
        $this->repository = $repository;
        $this->helper = $helper;
    }
    /**
     * Retrieve option array with empty value
     *
     * @return string[]
     */
    public function getAllOptions()
    {

        if ($this->_options === null) {
               $parentId = $this->helper->getCaetgoryId($this->configField);
               $parent = $this->repository->get($parentId);
               $this->_options[] = [
                            'label' => __("Select"),
                            'value' => ' '
                        ];
               foreach($parent->getChildrenCategories() as $category)
               {
                    $this->_options[] = [
                            'label' => $category->getName(),
                            'value' => $category->getId()
                        ];
                }

                /*uasort(
                    $this->_options,
                    function (array $optionA, array $optionB) {
                        return strcmp($optionA['label'], $optionB['label']);
                    }
                );*/
                $this->_options = array_values($this->_options);
            }
            return $this->_options;
    }

}
