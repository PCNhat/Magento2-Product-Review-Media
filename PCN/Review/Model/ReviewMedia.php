<?php

namespace PCN\Review\Model;

use Magento\Framework\Model\AbstractModel;
use PCN\Review\Model\ResourceModel\ReviewMedia as ReviewMediaResourceModel;

class ReviewMedia extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(ReviewMediaResourceModel::class);
    }
}
