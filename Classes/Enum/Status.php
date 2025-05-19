<?php

namespace MFR\T3PromClient\Enum;

enum Status
{
    case REQUEST_DISPATCHED;
    case REQUEST_RECEIVED;
    case REQUEST_PROCESSED;
}