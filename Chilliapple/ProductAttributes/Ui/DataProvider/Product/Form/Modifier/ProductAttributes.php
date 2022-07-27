<?php

/**
 * Chilliapple_ProductAttributes extension
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category  Chilliapple
 * @package   Chilliapple_ProductAttributes
 * @copyright Copyright (c) 2020
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 */

namespace Chilliapple\ProductAttributes\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Ui\Component\Form\Fieldset;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\DynamicRows;
use Magento\Ui\Component\Container;
use Magento\Ui\Component\Form\Element\ActionDelete;
use Magento\Ui\Component\Form\Element\Select;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\DataType\Number;
use Magento\Ui\Component\Form\Element\DataType\Price;

use Chilliapple\ProductAttributes\Model\CoinHighlights;
use Chilliapple\ProductAttributes\Model\EncyclopediaProducts;

class ProductAttributes extends AbstractModifier
{

    private $locator;

    public function __construct(
        LocatorInterface $locator,
        CoinHighlights $coinHighlights,
        EncyclopediaProducts $encyclopediaProducts
    ){
        $this->locator = $locator;
        $this->coinHighlights = $coinHighlights;
        $this->encyclopediaProducts = $encyclopediaProducts;
    }

    public function modifyData(array $data)
    {
        $product = $this->locator->getProduct();

        if($productId = $product->getId()){

            $items = $this->coinHighlights->getCoinHighlightsAttributeByProduct($product);
            $coinHighlight = $this->makeArrayCoinHighlightAttribute($items);

            $items = $this->encyclopediaProducts->getEncRelatedProductAttributeByProduct($product);
            $encRelatedProduct = $this->makeArrayEncRelatedProductAttribute($items);
                
             $data = array_replace_recursive(

                 $data,

                 [
                     $productId => [

                         'product' => [
                            'custom_attributes_fieldset' => [

                                 'coin_highlight' => $coinHighlight,
                                 'enc_related_product' => $encRelatedProduct,
                            ]

                         ]

                     ]

                 ]

             );
        }

        return $data;
    }

    public function modifyMeta(array $meta)
    {
        $meta = array_replace_recursive(
            $meta,
            [
                'custom_attributes_fieldset' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __('Custom Attributes'),
                                'componentType' => Fieldset::NAME,
                                'dataScope' => 'data.product.custom_attributes_fieldset',
                                'collapsible' => true,
                                'sortOrder' => 5,
                            ],
                        ],
                    ],
                    'children' => [
                            "coin_highlight" => $this->getCoinHighlightStructure(),
                            "enc_related_product" => $this->getEncRelatedProductStructure()
                    ],
                ]
            ]
        );

        return $meta;
    }

    private function getCoinHighlightStructure()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => 'dynamicRows',
                        'component' => 'Magento_Catalog/js/components/dynamic-rows-tier-price',
                        'label' => __('Product Coin Highlights'),
                        'renderDefaultRecord' => false,
                        'recordTemplate' => 'record',
                        'dataScope' => '',
                        'dndConfig' => [
                            'enabled' => false,
                        ],
                        'disabled' => false,
                        'required' => false,
                        'sortOrder' => 10
                    ],
                ],
            ],
            'children' => [
                'record' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Container::NAME,
                                'isTemplate' => true,
                                'is_collection' => true,
                                'component' => 'Magento_Ui/js/dynamic-rows/record',
                                'dataScope' => '',
                            ],
                        ],
                    ],
                    'children' => [
                        'coin_highlight' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'formElement' => Input::NAME,
                                        'componentType' => Field::NAME,
                                        'dataType' => Number::NAME,
                                        'label' => __('Product Coin Highlights'),
                                        'dataScope' => 'coin_highlight',
                                        'sortOrder' => 20,
                                        'validation' => [
                                            'required-entry' => true,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'sort_order' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'formElement' => Input::NAME,
                                        'componentType' => Field::NAME,
                                        'dataType' => Number::NAME,
                                        'label' => __('Sort Order'),
                                        'dataScope' => 'sort_order',
                                        'sortOrder' => 30,
                                        'validation' => [
                                            'required-entry' => true,
                                            'validate-greater-than-zero' => true,
                                            'validate-digits' => true,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'actionDelete' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'componentType' => 'actionDelete',
                                        'dataType' => Text::NAME,
                                        'label' => '',
                                        'sortOrder' => 50,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    private function getEncRelatedProductStructure()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => 'dynamicRows',
                        'component' => 'Magento_Catalog/js/components/dynamic-rows-tier-price',
                        'label' => __('Encyclopedia Related Products'),
                        'renderDefaultRecord' => false,
                        'recordTemplate' => 'record',
                        'dataScope' => '',
                        'dndConfig' => [
                            'enabled' => false,
                        ],
                        'disabled' => false,
                        'required' => false,
                        'sortOrder' => 20
                    ],
                ],
            ],
            'children' => [
                'record' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Container::NAME,
                                'isTemplate' => true,
                                'is_collection' => true,
                                'component' => 'Magento_Ui/js/dynamic-rows/record',
                                'dataScope' => '',
                            ],
                        ],
                    ],
                    'children' => [
                        'enc_related_product' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'formElement' => Input::NAME,
                                        'componentType' => Field::NAME,
                                        'dataType' => Number::NAME,
                                        'label' => __('Encyclopedia Related Products'),
                                        'dataScope' => 'enc_related_product',
                                        'sortOrder' => 20,
                                        'validation' => [
                                            'required-entry' => true,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'sort_order' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'formElement' => Input::NAME,
                                        'componentType' => Field::NAME,
                                        'dataType' => Number::NAME,
                                        'label' => __('Sort Order'),
                                        'dataScope' => 'sort_order',
                                        'sortOrder' => 30,
                                        'validation' => [
                                            'required-entry' => true,
                                            'validate-greater-than-zero' => true,
                                            'validate-digits' => true,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'actionDelete' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'componentType' => 'actionDelete',
                                        'dataType' => Text::NAME,
                                        'label' => '',
                                        'sortOrder' => 50,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }


    protected function makeArrayCoinHighlightAttribute($items){

        $data = [];

        if(count($items)){

            $i = 0;
            foreach($items as $item){
                $data[$i]['coin_highlight'] = $item->getCoinHighlight();
                $data[$i]['sort_order']     = $item->getSortOrder();
                $i++;
            }
        }

        return $data;
    }

    protected function makeArrayEncRelatedProductAttribute($items){

        $data = [];

        if(count($items)){

            $i = 0;
            foreach($items as $item){
                $data[$i]['enc_related_product'] = $item->getEncRelatedProduct();
                $data[$i]['sort_order']     = $item->getSortOrder();
                $i++;
            }
        }

        return $data;
    }

}