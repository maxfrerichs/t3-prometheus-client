# TYPO3 Extension 'typo3-prometheus'

## Note: This extension is under development. Exposing metrics to public is dangerous. Be care 

## About this extension:
This extension converts system status informations to Prometheus-readable metrics.

## Requirements
* TYPO3 12.4 or higher
* System extensions "cms-reports" installed (it will be automatically installed)
* PHP 8.2 or higher

# How it works
* TYPO3 has a system extension called "cms-reports" that provides information about your TYPO3 system and the enviroment. While system reports are displayed in a backend module, reports are not exposed to public.
* "cms-reports" uses a registry that can be used to retrieve every registered status provider in your installation. Every status provider can provide one or multiple status reports.
* Every status report consists of title, severity, message and value.
* severity is a numeric value ranging from -1 (info) to 2 (error) that can be used to generate gauge metrics.
  "Gauge" is one of the core metric types offered by the client library.

## How to use:
* Install extension with ```composer req maxfrerichs/typo3-prometheus``` in your favourite TYPO3 distribution
* Add "your-site.com/metrics" to your prometheus config

## TODO:
* Add some auth-key based authentication if you need to expose metrics to the outside
* Add support for other metric types (maybe).
* Testing and releasing.