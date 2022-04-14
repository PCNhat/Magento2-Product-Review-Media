<?php

namespace PCN\Review\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use PCN\Review\Helper\Constant;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * @var File
     */
    protected File $io;

    /**
     * @var DirectoryList
     */
    protected DirectoryList $directoryList;

    /**
     * InstallSchema Constructor
     *
     * @param File $io
     * @param DirectoryList $directoryList
     */
    public function __construct(File $io, DirectoryList $directoryList)
    {
        $this->io = $io;
        $this->directoryList = $directoryList;
    }

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->createReviewImagesDirectory();

        $installer = $setup;
        $installer->startSetup();
        $tableName = $installer->getTable(Constant::REVIEW_MEDIA_TABLE_NAME);

        $table = $installer->getConnection()
            ->newTable($tableName)
            ->addColumn(
                'image_id',
                Table::TYPE_BIGINT,
                null,
                [
                    'identity' => true,
                    'nullable' => false,
                    'primary' => true,
                    'unsigned' => true
                ],
                'Primary id of table'
            )->addColumn(
                'review_id',
                Table::TYPE_BIGINT,
                null,
                [
                    'nullable' => false,
                    'unsigned' => true
                ],
                'Foreign key for review id'
            )->addColumn(
                'media_url',
                Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false
                ],
                'Media URL'
            )->addForeignKey(
                $installer->getFkName($tableName, 'review_id', 'review', 'review_id'),
                'review_id',
                $installer->getTable('review'),
                'review_id',
                Table::ACTION_CASCADE
            );

        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }

    private function createReviewImagesDirectory()
    {
        $this->io->mkdir($this->directoryList->getPath(DirectoryList::MEDIA) . '/' . Constant::REVIEW_MEDIA_FOLDER, 0755);
    }
}
