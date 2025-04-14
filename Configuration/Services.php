<?php

declare(strict_types=1);

namespace MFR\T3PromClient\MetricList;

use MFR\T3PromClient\Metrics\MetricInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container, ContainerBuilder $containerBuilder) {
    $containerBuilder->registerForAutoconfiguration(MetricInterface::class)->addTag('prometheus.metric');
};