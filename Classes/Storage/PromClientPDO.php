<?php
namespace MFR\T3PromClient\Storage;
use PDO;

class PromClientPDO extends PDO
{

    public function __construct()
    {
        $host = $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['host'];
        $name = $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['dbname'];
        $user = $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['user'];
        $pass = $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['password'];
        parent::__construct("mysql:host=".$host.";dbname=".$name, $user, $pass);
    }
}