<?php
namespace Magecomp\Mobilelogin\Model\Config;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
/**
 * Class Image
 * Magecomp\Mobilelogin\Model\Config
 */
class Image extends \Magento\Config\Model\Config\Backend\Image
{
    const UPLOAD_DIR = 'mobilelogin/image';

     protected $_maxFileSize = 0;

    /**
     * @var Filesystem
     */
    protected $_filesystem;

    /**
     * @return string

     */

   
    protected function _getUploadDir()
    {
        return $this->_mediaDirectory->getAbsolutePath($this->_appendScopeInfo(self::UPLOAD_DIR));
    }

    /**
     * @return bool
     */
    protected function _addWhetherScopeInfo()
    {
        return true;
    }

    /**
     * @return array|string[]
     */
    protected function _getAllowedExtensions()
    {
        return ['jpg', 'jpeg', 'gif', 'png','JPG'];
    }

     public function validateMaxSize($filePath)
    {
         $directory = $this->_filesystem->getDirectoryRead(DirectoryList::SYS_TMP);
        if ($directory->stat(
            $directory->getRelativePath($filePath)
        )['size'] > 100000
        ) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('The file you\'re uploading exceeds the server size limit of 100 kilobytes.',)
            );
        }
    }
    
}
