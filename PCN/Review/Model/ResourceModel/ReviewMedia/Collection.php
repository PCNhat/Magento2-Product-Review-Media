<?php

namespace PCN\Review\Model\ResourceModel\ReviewMedia;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use PCN\Review\Model\ReviewMedia as ReviewMediaModel;
use PCN\Review\Model\ResourceModel\ReviewMedia as ReviewMediaResourceModel;

class Collection extends AbstractCollection
{
    /**
     * constructor
     *
     */
    protected function _construct()
    {
        $this->_init(ReviewMediaModel::class,ReviewMediaResourceModel::class);
    }
}
