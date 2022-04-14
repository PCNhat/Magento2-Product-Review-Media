<?php

namespace PCN\Review\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use PCN\Review\Helper\Constant;

class ReviewMedia extends AbstractDb
{
    protected function _construct()
    {
        $this->_init(Constant::REVIEW_MEDIA_TABLE_NAME, 'image_id');
    }
}
