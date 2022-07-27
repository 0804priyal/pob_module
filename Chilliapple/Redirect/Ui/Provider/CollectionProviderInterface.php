<?php
namespace Chilliapple\Redirect\Ui\Provider;

interface CollectionProviderInterface
{
    /**
     * @return \Chilliapple\Redirect\Model\ResourceModel\AbstractCollection
     */
    public function getCollection();
}
