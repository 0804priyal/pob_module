<?php

declare(strict_types=1);

namespace Chilliapple\Governments\Ui;

interface CollectionProviderInterface
{
    /**
     * @return \Chilliapple\Governments\Model\ResourceModel\AbstractCollection
     */
    public function getCollection();
}
