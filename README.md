Nours RestAdminBundle
=====================

This bundle is made to simplify the implementation of rich back-office interfaces or front applications. It implements
infrastructure tools configured around the representations of resources, and built on top of the Symfony2 framework.
The core is composed of a REST API building system, which enables to provide reusable actions for different kinds of
resources. 

Installation
============

Add to composer.json :

"nours/rest-admin-bundle": "dev-master"

Add bundle to AppKernel :

new Nours\RestAdminBundle\NoursRestAdminBundle()

Create main resources config file, for example in app/config/resources.yml, then configure it in bundle main configuration :

nours_rest_admin:
    resource: '%kernel.root_dir%/config/resources.yml'