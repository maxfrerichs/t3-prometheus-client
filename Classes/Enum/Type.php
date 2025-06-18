<?php
declare(strict_types=1);

namespace MFR\T3PromClient\Enum;

enum Type
{
    case GAUGE;
    case COUNTER;
    case HISTOGRAM;
    case SUMMARY;
}
