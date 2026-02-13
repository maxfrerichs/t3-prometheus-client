.. _server:

Server configuration
====================

An example configuration for Prometheus exporter could look like this:

.. code-block:: apache

    <VirtualHost *:9090>

        ServerAdmin webmaster@localhost
        DocumentRoot ${APACHE_DOCUMENT_ROOT}
        RequestHeader set Host "your-domain.com"

        <Directory ${APACHE_DOCUMENT_ROOT}>
            AllowOverride All
            Options -Indexes +FollowSymLinks -MultiViews
            # config for php-fpm
            <IfModule mod_proxy_fcgi.c>
                Options  -ExecCGI
                <FilesMatch "\.php$">
               SetHandler "proxy:fcgi://127.0.0.1:9126/"
            </FilesMatch>
                </IfModule>
                # config for php cgid
                <IfModule mod_fcgid.c>
                    AddHandler fcgid-script .php
                    Options +ExecCGI
                    FcgidWrapper /usr/bin/php-wrapper .php
                </IfModule>

            Order allow,deny
                allow from all

                <IfVersion >= 2.4>
                   Require all granted
                </IfVersion>
        </Directory>

        Include logging.conf
    </VirtualHost>

Best practices:
===============
* Use a port that is not associated with default ports.
* Set Host header to prevent trustedHostsPattern mismatches
* Do not export the port to the public. Prometheus is meant to run on the same machine as your application.