<?php
namespace Aheadworks\OneStepCheckout\Model\Address\Form\GeoIp\File;

use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem\DriverPool;

/**
 * Class Downloader
 * @package Aheadworks\OneStepCheckout\Model\Address\Form\GeoIp\File
 */
class Downloader
{
    /**
     * @var DriverPool
     */
    private $driverPool;

    /**
     * @var Info
     */
    private $fileInfo;

    /**
     * @param DriverPool $driverPool
     * @param Info $fileInfo
     */
    public function __construct(
        DriverPool $driverPool,
        Info $fileInfo
    ) {
        $this->driverPool = $driverPool;
        $this->fileInfo = $fileInfo;
    }

    /**
     * Download file into specific folder
     *
     * @param string $path
     * @param string $pathToSave Relative path
     * @return void
     * @throws FileSystemException
     */
    public function download(string $path, string $pathToSave): void
    {
        $httpsDriver = $this->driverPool->getDriver(DriverPool::HTTPS);
        $fileDriver = $this->driverPool->getDriver(DriverPool::FILE);
        if ($httpsDriver->isExists($path)) {
            $absoluteBasePath = $this->fileInfo->getAbsolutePath();
            if (!$fileDriver->isExists($absoluteBasePath)) {
                $fileDriver->createDirectory($absoluteBasePath);
            }
            $httpsDriver->copy($path, $this->fileInfo->getAbsolutePath($pathToSave), $fileDriver);
        }
    }
}
