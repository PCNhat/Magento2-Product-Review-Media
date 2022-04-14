<?php

namespace PCN\Review\Observer;

use Exception;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use Magento\MediaStorage\Model\File\UploaderFactory;
use PCN\Review\Helper\Constant;
use PCN\Review\Model\ReviewMediaFactory;
use Zend_Log;
use Zend_Log_Exception;
use Zend_Log_Writer_Stream;

class ProductReviewSaveAfter implements ObserverInterface
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
     * @var UploaderFactory
     */
    protected UploaderFactory $uploaderFactory;

    /**
     * @param RequestInterface $request
     * @param ReviewMediaFactory $reviewMediaFactory
     * @param Filesystem $filesystem
     * @param UploaderFactory $uploaderFactory
     */
    public function __construct(
        RequestInterface   $request,
        ReviewMediaFactory $reviewMediaFactory,
        Filesystem         $filesystem,
        UploaderFactory    $uploaderFactory
    )
    {
        $this->request = $request;
        $this->reviewMediaFactory = $reviewMediaFactory;
        $this->filesystem = $filesystem;
        $this->uploaderFactory = $uploaderFactory;
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

        $mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $target = $mediaDirectory->getAbsolutePath(Constant::REVIEW_MEDIA_FOLDER);

        $reviewId = $observer->getEvent()->getObject()->getReviewId();
        $media = $this->request->getFiles('review_media');

        if ($media) {
            try {
                for ($i = 0; $i < count($media); $i++) {
                    $uploader = $this->uploaderFactory->create(['fileId' => 'review_media[' . $i . ']']);
                    $uploader->setAllowedExtensions(['jpg', 'jpeg', 'png']);
                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(true);
                    $uploader->setAllowCreateFolders(true);

                    $result = $uploader->save($target);

                    $reviewMedia = $this->reviewMediaFactory->create();
                    $reviewMedia->setMediaUrl($result['file']);
                    $reviewMedia->setReviewId($reviewId);
                    $reviewMedia->save();
                }
            } catch (Exception $e) {
                if ($e->getCode() == 0) {
                    $logger->err($e->getMessage());
                    $this->messageManager->addError("Something went wrong while saving review attachment(s).");
                }
            }
        }
    }
}
