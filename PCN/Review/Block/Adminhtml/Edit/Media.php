<?php

namespace PCN\Review\Block\Adminhtml\Edit;

use Magento\Backend\Block\Template;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use PCN\Review\Helper\Constant;
use PCN\Review\Model\ReviewMediaFactory;

class Media extends Template
{
    /**
     * @var ReviewMediaFactory
     */
    protected ReviewMediaFactory $reviewMediaFactory;

    /**
     * @param Template\Context $context
     * @param ReviewMediaFactory $reviewMediaFactory
     */
    public function __construct(
        Template\Context $context,
        ReviewMediaFactory $reviewMediaFactory
    )
    {
        $this->reviewMediaFactory = $reviewMediaFactory;
        $this->setTemplate("media.phtml");

        parent::__construct($context);
    }

    public function getMediaCollection()
    {
        return $this->reviewMediaFactory->create()
            ->getCollection()
            ->addFieldToFilter('review_id', $this->getRequest()->getParam('id'));
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getReviewMediaUrl(): string
    {
        return $this->_storeManager->getStore()
            ->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . Constant::REVIEW_MEDIA_FOLDER;
    }
}
