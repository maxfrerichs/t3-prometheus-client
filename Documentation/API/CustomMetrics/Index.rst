.. _custommetrics:

Register custom metrics
==============

Classes that implement `MFR\T3PromClient\Metrics\MetricsInterface` are automatically tagged by the Dependency Injection container and added to the Metrics registry,
allowing the registration of custom metrics by extension developers.

..  rst-class:: bignums-xxl
1. Create a new PHP class that implements `MFR\T3PromClient\Metrics\MetricsInterface`
2. Implement all contracts.
2. Flush all TYPO3 caches.
