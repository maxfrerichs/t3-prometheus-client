# TYPO3 Extension 't3-prometheus-client'

[![License](https://img.shields.io/github/license/maxfrerichs/t3-prometheus-client)](LICENSE)
[![TYPO3](https://img.shields.io/badge/TYPO3-12%20%7C%2013-orange.svg)](https://get.typo3.org/)

## About this extension

This extension provides a Prometheus exporter for TYPO3, enabling monitoring and metrics collection from your TYPO3 installation.
It exposes application metrics in Prometheus format, allowing integration with Prometheus servers and Grafana dashboards for comprehensive monitoring and alerting.

## Features

- **Automatic metric discovery** via Dependency Injection, allowing easy integration of third-party metrics.
- **Default metrics** for TYPO3 health monitoring (updates, failed logins, scheduler tasks, etc.)
- **Easy extensibility** - create custom metrics in your own extensions
- **PSR-14 events** for filtering and modifying metrics at runtime
- **Support for authentication** (none, basic, token-based)

## Requirements

* TYPO3 12.4 or 13.x
* PHP 8.2 or higher
* [Prometheus](https://prometheus.io/) server

## Installation

```bash
composer require maxfrerichs/t3-prometheus-client
```

Then activate the extension in the Extension Manager or via CLI:

```bash
vendor/bin/typo3 extension:activate t3_prometheus_client
```

## Quick Start

1. **Configure the extension** in Settings → Extension Configuration → `t3_prometheus_client`
2. **Configure your web server** (Apache/Nginx) to expose the metrics port
3. **Configure Prometheus** to scrape your TYPO3 endpoint (default: `http://your-domain:9090/metrics`)
4. **(Optional)** Set up authentication to secure your metrics endpoint

See the [Installation](Documentation/Introduction/Installation/Index.rst) and [Server Setup](Documentation/Introduction/Server/Index.rst) documentation for detailed instructions.

## Default Metrics

The extension ships with ready-to-use metrics:

- **Available Updates** - Number of TYPO3 core updates available
- **Failed Logins** - Track potential security issues
- **Service Availability** - Monitor ServiceUnavailableException occurrences
- **Failed Scheduler Tasks** - Monitor scheduler task health

See the [Metrics documentation](Documentation/Introduction/Metrics/Index.rst) for complete details.

## Extending with Custom Metrics

Creating custom metrics is straightforward:

```php
<?php
namespace Vendor\YourExtension\Metrics;

use MFR\T3PromClient\Enum\MetricType;
use MFR\T3PromClient\Metrics\AbstractMetric;

final class MyCustomMetric extends AbstractMetric
{
    protected string $name = 'my_custom_metric';
    protected MetricType $type = MetricType::GAUGE;
    protected string $help = 'Description of my metric';

    public function getValue(): int
    {
        return 42; // Your calculation here
    }
}
```

Afterwards, flush caches to re-build the DI container.
Note: Any class implementing `MetricInterface` is automatically registered.

## Modifying Metrics at Runtime

Use PSR-14 events to filter, add, or modify metrics before they're rendered:

```php
use MFR\T3PromClient\Event\BeforeMetricsRenderedEvent;
use TYPO3\CMS\Core\Attribute\AsEventListener;

#[AsEventListener(identifier: 'my-extension/filter-metrics')]
final readonly class FilterMetricsListener
{
    public function __invoke(BeforeMetricsRenderedEvent $event): void
    {
        $metrics = $event->getMetrics();
        // Filter, modify, or add metrics
        $event->setMetrics($metrics);
    }
}
```

## Documentation

Complete documentation is available on https://docs.typo3.org/p/maxfrerichs/t3-prometheus-client/


## Known Issues

Please report issues on [GitHub Issues](https://github.com/maxfrerichs/t3-prometheus-client/issues).

## Contributing

Contributions are welcome!

Please open an issue or pull request on GitHub, or contact:
- Email: maxfrerichs@gmx.de / max.frerichs@lfda.de
- GitHub: https://github.com/maxfrerichs/t3-prometheus-client

## License

This project is licensed under the terms specified in the [LICENSE](LICENSE) file.

---
