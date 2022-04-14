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
use Zend_Log;
use Zend_Log_Exception;
use Zend_Log_Writer_Stream;

class AdminProductReviewSaveAfter implements ObserverInterface
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

    /**
     * @param Observer $observer
     * @throws FileSystemException
     * @throws Zend_Log_Exception
     */
    public function execute(Observer $observer)
    {
        $writer = new Zend_Log_Writer_Stream(BP . '/var/log/review_media.log');
        $logger = new Zend_Log();
        $logger->addWriter($writer);

        $target = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA)->getAbsolutePath(Constant::REVIEW_MEDIA_FOLDER);

        $deletedMediaString = $this->request->getParam('deleted_media');

        if ($deletedMediaString) {
            try {
                $ids = explode(',', trim($deletedMediaString, ','));
                foreach ($ids as $id) {
                    $reviewMedia = $this->reviewMediaFactory->create()->load($id);
                    $path = $target . $reviewMedia->getMediaUrl();
                    if ($this->file->isExists($path)) {
                        $this->file->deleteFile($path);
                    }

                    $reviewMedia->delete();
                }
            } catch (Exception $e) {
                $logger->err($e->getMessage());
                $this->messageManager->addException($e, __('Something went wrong while updating review attachment(s).'));
            }
        }

    }
}
