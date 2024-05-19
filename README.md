# TYPO3 Extension 'typo3-prometheus'

## Note: This extension is under active development. Don't use it in production context unless you really know what you're doing and know, how to prevent exposing metrics to public

## About this extension:
This extension generates Prometheus-readable metrics from system status informations (more info on "How it works" and exposes them on /metrics.

## Requirements
* TYPO3 12.4 or higher
* System extensions "cms-reports" installed (it will be automatically installed)
* PHP 8.2 or higher
* Prometheus (obviously)

# How it works
* TYPO3 has a system extension called "cms-reports" that provides information about your TYPO3 system and the enviroment. While system reports are displayed in a backend module, reports are not exposed to public.
* "cms-reports" uses a registry that can be used to retrieve every registered status provider in your installation. Every status provider can provide one or multiple status reports.
* Every status report consists of title, severity, message and value.
* severity is a numeric value ranging from -1 (info) to 2 (error) that can be used to generate gauge metrics.
  "Gauge" is one of the core metric types offered by the client library.

## How to use:
* Install extension with ```composer req maxfrerichs/typo3-prometheus``` in your favourite TYPO3 distribution
* Add "your-site.com/metrics" to your prometheus config
* Have fun!

# Known issues
Using StatusRegistry class directly via Dependency Injection causes Extbase frontend plugins to break, as ConfigurationManager is invoked at some point without $GLOBALS['TYPO3_REQUEST'] being set, causing it to fallback to BackendConfigurationManager, while we are in frontend. This prevents us from automatically aggregating all registered Status reports as Prometheus metrics

## API [WIP]
Registration of custom StatusProvider classes is possible by using the CustomStatusProviderEvent.

## TODO:
* Add some auth-key based authentication to /metrics endpoint (if someone needs to access metrics from outside)
* Add support for other metric types (maybe).
* Add some quality-assuring stuff like static code-analysis (it's not that much code actually).
* Testing and releasing on packagist.org and extensions.typo3.org
* Add proper documentation

## Contribution:
Just do it. Open an issue, tell me your ideas, request a specific feature, report bugs and so on. You can also write an e-mail to "typo3@maxfrerichs.dev" :-)
