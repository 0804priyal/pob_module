<?php

namespace Chilliapple\Redirect\Source;

class RedirectType implements \Magento\Framework\Option\ArrayInterface
{
    const NO = '0';
    const TEMPORARY = '302';
    const PERMANENT = '301';
    /**
     * Options
     * 
     * @var array
     */
    protected $options;

    /**
     * Retrieve all Items as an option array
     *
     * @return array
     * @throws StateException
     */
    public function getAllOptions()
    {
        if (empty($this->options)) {
                $options = [];
                
                $options[] = [
                    'value' => self::NO,
                    'label' => __('No'),
                ];
                
                $options[] = [
                    'value' => self::TEMPORARY,
                    'label' => __('Temporary (302)'),
                ];

                $options[] = [
                    'value' => self::PERMANENT,
                    'label' => __('Permanent (301)'),
                ];

            $this->options = $options;
        }

        return $this->options;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->getAllOptions();
    }

    /**
     * @return array
     */
    public function getAllValues()
    {
        $options = $this->getAllOptions();
        $values  = [];

        foreach($options as $option){
            $values[] = $option['value'];
        }
        
        return $values;
    }
}
