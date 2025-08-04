<?php

/**
 * Wrapper for the PHPExcel library.
 */
class XPHPExcel extends CComponent {

    private static $_isInitialized = false;

    /**
     * Register autoloader.
     */
    public static function init() {
        if (!self::$_isInitialized) {
            spl_autoload_unregister(array('YiiBase', 'autoload'));
            require(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Classes' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
            spl_autoload_register(array('YiiBase', 'autoload'));
            PHPExcel_Shared_File::setUseUploadTempDirectory(TRUE);
            self::$_isInitialized = true;
        }
    }

    /**
     * Returns new PHPExcel object. Automatically registers autoloader.
     * @return PHPExcel
     */
    public static function createPHPExcel() {
        self::init();
        return new PHPExcel;
    }

}

?>
