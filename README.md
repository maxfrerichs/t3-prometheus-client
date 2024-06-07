# TYPO3 Extension 'typo3-prometheus'

## About this extension:
This extension generates Prometheus-readable metrics from system status informations (more info on "How it works") and exposes them on an endpoint.

## Requirements
* TYPO3 12.4 or higher
* PHP 8.0 or higher

# How it works
* TYPO3 has a system extension called "cms-reports" that provides information about your TYPO3 system and the enviroment. While system reports are displayed in a backend module, reports are not exported in any way.
* "cms-reports" provides an API that can be used to register status providers in your installation. Every status provider can provide one or multiple status reports.
* Every status report consists of title, severity, message and value.
* severity is a numeric value ranging from -1 (info) to 2 (error) that can be used to generate gauge metrics.
  "Gauge" is one of the core metric types offered by the Prometheus client library.

## How to use:
* Install extension with ```composer req maxfrerichs/typo3-prometheus``` in your favourite TYPO3 distribution
* Configure your web server to serve your TYPO3 on a non-HTTP port, default is 9090. **Be advised**: Do not expose this port to the web, this extension provides no built-in authentication mechanism to limit access from public. You should implement your own security means if you want to expose the endpoint to the web.
* Install and configure prometheus on the same web server. Please refer to the [Prometheus documentation](https://prometheus.io/docs/introduction/overview/) for more information regarding this topic

# Known issues
* Using the StatusRegistry class directly via Dependency Injection causes Extbase frontend plugins to break, as ConfigurationManager is invoked at some point without $GLOBALS['TYPO3_REQUEST'] being set, causing it to fallback to BackendConfigurationManager, while we are in frontend. This prevents us from automatically aggregating all registered Status reports as Prometheus metrics and some status reports relying on Extbase functionality cannot be used.

## API
Registration of custom StatusProvider classes is possible by creating event listeners listening to the RegisterStatusProviderEvent and call $event->injectStatusProvider() in your event listener.
Note: When using TYPO3 11.5, you may need to make classes implementing StatusProviderInterface public in your Configuration/Service.yaml file. 

## TODO:
* Add support for other metric types (optional).
* Add support for older TYPO3 versions.
* Add more metrics

## Contribution:
Just do it. Open an issue, tell me your ideas, request a specific feature, report bugs and so on. You can also write an e-mail to "typo3@maxfrerichs.dev" :-)
