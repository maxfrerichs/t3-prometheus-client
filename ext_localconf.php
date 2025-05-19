<?php

use MFR\T3PromClient\Message\PromClientRequestMessage;

unset($GLOBALS['TYPO3_CONF_VARS']['SYS']['messenger']['routing']['*']);
$GLOBALS['TYPO3_CONF_VARS']['SYS']['messenger']['routing'][PromClientRequestMessage::class] = 'doctrine';