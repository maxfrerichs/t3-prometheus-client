<?php
declare(strict_types=1);
namespace MFR\T3PromClient\Message;

use MFR\T3PromClient\Enum\Status;

class PromClientRequestMessage
{
    public function __construct(
        private readonly Status $status
    ){}
}