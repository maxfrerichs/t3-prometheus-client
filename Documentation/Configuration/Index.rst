.. _configuration:

Configuration
=============

The extension provides several configuration options that can be set in the Extension Configuration section of the TYPO3 Settings.

.. note::

   The configuration file is located at :file:`ext_conf_template.txt`.

General Settings
----------------

.. confval:: port

   :type: int
   :default: 9090
   :label: Port for Prometheus

   The port on which the Prometheus exporter will be accessible.

.. confval:: path

   :type: string
   :default: /metrics
   :label: URL path for Prometheus

   The URL path where the Prometheus metrics will be exposed.

.. confval:: debug

   :type: boolean
   :default: false
   :label: Debug mode

   Enables debug mode. This may provide additional output useful for troubleshooting.

.. confval:: pushGateway

   :type: string
   :default: (empty)
   :label: Push-gateway location

   Optional URL of a Prometheus Push gateway. If provided, metrics can be pushed instead of pulled.

Authentication
--------------

.. confval:: mode

   :type: options [none, basic, token]
   :default: token
   :label: Authentication mode

   Select the authentication mode to protect the metrics endpoint:

   - **none**: No authentication
   - **basic**: Basic authentication (username and password)
   - **token**: Token-based authentication

.. confval:: token

   :type: string
   :default: (empty)
   :label: Authentication token

   Used only when token-based authentication is selected. Specify the token clients must use to access metrics.

.. confval:: basicAuth.username

   :type: string
   :default: (empty)
   :label: User name

   Username for basic authentication. Only relevant if authentication mode is set to `basic`.

.. confval:: basicAuth.password

   :type: string
   :default: (empty)
   :label: Password

   Password for basic authentication. Only relevant if authentication mode is set to `basic`.
