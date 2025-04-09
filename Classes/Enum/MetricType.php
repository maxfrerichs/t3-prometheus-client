<?php

namespace MFR\T3PromClient\Enum;


enum MetricType
{
    case GAUGE;
    case COUNTER;
    case HISTOGRAM;
    case SUMMARY;
}