<?php

/**
 * This is the configuration for generating message translations
 * for the Yii framework. It is used by the 'yiic message' command.
 */
return array(
    'sourcePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'messagePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'messages',
    //2022/07/07 modified
    //'languages' => array('en', 'es', 'fr', 'it'),
    'languages' => array('en', 'ja'),
    'fileTypes' => array('php'),
    'overwrite' => true,
    'exclude' => array(
        '/backups',
        '/data',
        '/extensions',
        '/messages',
        '/runtime',
        '/tests',
        '.svn',
        'yiic.php',
    ),
    'sort' => true,
);
