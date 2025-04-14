# TYPO3 Extension 't3-prometheus-client'

## About this extension:
This extension provides a client for Prometheus to access and process timeseries data from system status informations (more info on "How it works"). 
Both scraping and pushing to a gateway are supported.

## Requirements
* TYPO3 12.4 or higher
* PHP 8.2 or higher
* [Prometheus](https://prometheus.io/)

## How to use:
* Install extension with ```composer req maxfrerichs/t3-prometheus-client``` in your TYPO3 distribution
* Install and configure prometheus. Please refer to the [Prometheus documentation](https://prometheus.io/docs/introduction/overview/) for more information regarding this topic.
* Configure the extension. The "DevOps' way to use Prometheus is to expose the app on a separate, non-public port and deploy Prometheus on the same webserver.
  Exposing the TYPO3 instance on a separate port can be achieved with a VirtualHost config (if you're using apache2).
  However, you can also configure this extension to provide Basic authentication or Token-based authentication, so you can expose the /metrics endpoint securely to the public, if needed.
* Configure your host. If you want to run Prometheus on the same server, you need to create a VirtualHost config for the non-public port. An example for this config can be found in Examples/config/other-vhosts.example.config
  Make sure to set a Host header, otherwise TYPO3 will throw an Exception.
* Make sure that the Prometheus config and the extension config match.

# Known issues
(Your issue?)

## API
Registration of custom metrics is possible by creating a class that implements MFR\T3PromClient\Metrics\MetricInterface or by registering the service with the prometheus.metric tag in Configuration/Services.yaml

## TODO:
* Add support for other metric types [WIP].
* Add more default metrics [WIP]
* Write more documentation [WIP]

## Contribution:
Every contribution is appreciated. Open an issue, tell me your ideas, request a specific feature, report bugs and so on. You can also write an e-mail to "maxfrerichs@gmx.de" or "max.frerichs@lfda.de" :-)
