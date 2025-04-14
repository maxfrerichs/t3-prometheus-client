.. _installation:

Installation
============

The extension can be installed as any other extension of TYPO3 CMS. Get the
extension by one of the following methods:

#. **Use composer**: Run

   .. code-block:: bash

      composer require maxfrerichs/t3-prometheus-client

   in your TYPO3 installation.

#. **Get it from the Extension Manager:** Switch to the module :guilabel:`Admin Tools > Extensions`.
   Switch to :guilabel:`Get Extensions` and search for the extension key
   *t3_prometheus_client* and import the extension from the repository.

#. **Get it from typo3.org:** You can always get current version from `TER`_
   by downloading the zip version. Upload the file afterwards in the Extension
   Manager.

and :ref:`configure <configuration>` it.

.. _TER: https://extensions.typo3.org/extension/t3_prometheus_client/

Compatibility
-------------

Ensure the compatibility of the extension with your TYPO3 installation by
considering this compatibility matrix:

======================= =========== =========== ======================================
  t3-prometheus-client     TYPO3        PHP         Support / Development
======================= =========== =========== ======================================
  1.x                     12 - 13     8.2 - 8.4   active support
  0.x                     12 - 13     8.2 - 8.4   active support
======================= =========== =========== ======================================

Versioning
----------

.. note::

   While in 0.x, breaking changes are to be expected. API stability cannot be guaranteed. Be careful for running in production. 

