<?php

namespace PCN\Review\Observer;

use Exception;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Driver\File;
use PCN\Review\Helper\Constant;
use PCN\Review\Model\ReviewMediaFactory;

class AdminProductReviewDeleteBefore implements ObserverInterface
{
    /**
     * @var RequestInterface
     */
    protected RequestInterface $request;

    /**
     * @var ReviewMediaFactory
     */
    protected ReviewMediaFactory $reviewMediaFactory;

    /**
     * @var Filesystem
     */
    protected Filesystem $filesystem;

    /**
     * @var File
     */
    protected File $file;

    /**
     * @param RequestInterface $request
     * @param ReviewMediaFactory $reviewMediaFactory
     * @param Filesystem $filesystem
     * @param File $file
     */
    public function __construct(
        RequestInterface   $request,
        ReviewMediaFactory $reviewMediaFactory,
        Filesystem         $filesystem,
        File               $file
    )
    {
        $this->request = $request;
        $this->reviewMediaFactory = $reviewMediaFactory;
        $this->filesystem = $filesystem;
        $this->file = $file;
    }

    public function execute(Observer $observer)
    {
        /**
         * Single record deletion
         */
        $reviewId = $this->request->getParam('id', false);
        if ($reviewId) {
            $this->deleteReviewMedia($reviewId);
        }

        /**
         * Mass deletion
         */
        $reviewIds = $this->request->getParam('reviews', false);
        if ($reviewIds) {
            foreach ($reviewIds as $id) {
                $this->deleteReviewMedia($id);
            }
        }
    }

    /**
     * Execute delete media file
     *
     * @param int $reviewId
     * @throws FileSystemException
     */
    protected function deleteReviewMedia(int $reviewId)
    {
        $target = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA)->getAbsolutePath(Constant::REVIEW_MEDIA_FOLDER);

        try {
            $reviewMediaCollection = $this->reviewMediaFactory->create()
                ->getCollection()
                ->addFieldToFilter('review_id', $reviewId);

            foreach ($reviewMediaCollection as $reviewMedia) {
                $path = $target . $reviewMedia->getMediaUrl();
                if ($this->file->isExists($path)) {
                    $this->file->deleteFile($path);
                }
            }
        } catch (Exception $e) {
            $this->messageManager->addException($e, __('Something went wrong while deleting review(s) attachment(s).'));
        }
    }
}
