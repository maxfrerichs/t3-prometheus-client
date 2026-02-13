.. _introduction:

Introduction
============

t3_prometheus_client provides a client for Prometheus to access and process TYPO3-related application metrics.
It allows system administrators and DevOps to collect runtime metrics from TYPO3 installations, enabling seamless integration with Prometheus and Grafana for monitoring and alerting.
The client supports both scraping metrics from an endpoint and pushing metrics to a gateway, enabling a great variety of metrics to be monitored.

System requirements
===================

* t3_prometheus_client 0.9+ requires TYPO3 12 or 13.
* Prometheus (refer to https://prometheus.io for more information about Prometheus itself)
* Optional: Pushgateway
* Optional: Grafana for data visualization

Source code
===========

The source code is managed at

https://github.com/maxfrerichs/t3-prometheus-client


More pages
==========
.. toctree::
   :maxdepth: 2
   :titlesonly:

   Installation/Index
   Server/Index
   Metrics/Index
   Prometheus/Index

Continue with :ref:`installation` to set up the extension.
