# TYPO3 Extension 'typo3-prometheus'

## Note: This extension is under active development. Things are subject to change.

## About this extension:
This extension generates Prometheus-readable metrics from system status informations (more info on "How it works") and exposes them on /metrics.

## Requirements
* TYPO3 12.4 or higher
* System extensions "cms-reports" installed (it will be automatically installed)
* PHP 8.2 or higher
* Prometheus
* Web server configured to listen on a port that is not exposed to the outside (default 9090, can be changed in extension configuration).

# How it works
* TYPO3 has a system extension called "cms-reports" that provides information about your TYPO3 system and the enviroment. While system reports are displayed in a backend module, reports are not exposed to public.
* "cms-reports" provides an API that can be used to register status providers in your installation. Every status provider can provide one or multiple status reports.
* Every status report consists of title, severity, message and value.
* severity is a numeric value ranging from -1 (info) to 2 (error) that can be used to generate gauge metrics.
  "Gauge" is one of the core metric types offered by the Prometheus client library.

## How to use:
* Install extension with ```composer req maxfrerichs/typo3-prometheus``` in your favourite TYPO3 distribution
* Adjust trustedHostPattern config.
* Add "your-site.com/metrics" to your prometheus config (more information coming soon)
* Have fun!

# Known issues
* Using StatusRegistry class directly via Dependency Injection causes Extbase frontend plugins to break, as ConfigurationManager is invoked at some point without $GLOBALS['TYPO3_REQUEST'] being set, causing it to fallback to BackendConfigurationManager, while we are in frontend. This prevents us from automatically aggregating all registered Status reports as Prometheus metrics.
* Currently it's not possible to expose the /metrics endpoint to the public, as requests coming from default ports are blocked.

## API [WIP]
Registration of custom StatusProvider classes is possible by creating event listeners listening to CustomStatusProviderEvent.

## TODO:
* Add some kind of auth-key based authentication to allow exposing metrics to outside safely
* Add some quality-assuring stuff like static code-analysis (it's not that much code actually).
* Add support for other metric types (optional).
* Add documentation
* Testing and releasing

## Contribution:
Just do it. Open an issue, tell me your ideas, request a specific feature, report bugs and so on. You can also write an e-mail to "typo3@maxfrerichs.dev" :-)
