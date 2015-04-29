Nours RestAdminBundle
=====================

This bundle is made to simplify the implementation of rich back-office interfaces or front applications. It implements
infrastructure tools configured around the representations of resources, and built on top of the Symfony2 framework.
The core is composed of a resource REST API building mechanism, which enables to provide highly reusable components. 

Designed to be built on top of the domain model, an application will be composed of a lot of pages and treatments, some
of them being very common, like displaying collections or object resources (in HTML or JSON representations), forms for
creation, update, delete and the treatments behind these actions. Other should be enabled for specific entity behaviors 
like being publishable, positionable. Other actions will
be implemented for specific business jobs.

The action model proposed will allow to reuse implementations of any common or more or less specific tasks. A default
CRUD implementation is provided. It's behavior is based on a Doctrine2 repository bridge, can be 
reconfigured globally or for specific entities, for entity loading or persistence. On the treatment side, the Symfony2
forms will provide both input validation (at least CSRF protection) and data mapping to the entity.

The idea is to provide an event-driven application kernel. An event dispatcher 

From
the domain model perspective, this bundle enables actions, a kind of functional mixins, which will be used and configured among
the variety of domain entities.

On top of that mecanism, the bundle provides a set of REST compliant actions
 
 using a rest
based API for representing resources or actions and 

automating repetitive infrastructure tasks among them like routing,
handling forms, and implementing default behavior for operations.

A resource is basically some configuration applied to an entity, or any kind of php objects, which will be accessible
through actions. In fact, a resource action will be implemented using a set of controllers, configured using builders.
A builder is responsible for creating routing routes, and setup param converters, which will be able to load resources
based on routing params. The routes will also be mapped to concrete controllers implementations.

The bundle implements a default CRUD action set :
    * index
    * get
    * create
    * update
    * delete
 
The implementation of each one is isolated from each other, and will focus on its job : display data (on different
formats), and treat form data. All entities will be loaded using a default doctrine-based repository implementation.

Resources
=========

A resource is a simple configuration loaded from xml or annotations files, with a simple name, (eg. "post"). It has
some fields for all basic loading code, which will be explained later.

The representation knows all common details needed to implement actions. Each action have a default configuration,
which can be overridden (using options-resolver Symfony2 component), and will be used by builders to integrate routes.

The index and get actions are enabled by default on all resources, using a default usable configuration. They also can
be disabled if needed. The create, update, delete, and any custom actions, may be plugged as a kind of mixin like
mecanism.




Actions
=======

## index

The index action is the main entry point of the

Repository implementation
=========================

A repository is configured at resource level, and will typically have two responsibilities : find a single or a
collection of resource objects. The find is based on a identity retrieved from routing params (defaults to resource name)


```php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$routes = new RouteCollection();
$routes->add('hello', new Route('/hello', array('controller' => 'foo')));

$context = new RequestContext();

// this is optional and can be done without a Request instance
$context->fromRequest(Request::createFromGlobals());

$matcher = new UrlMatcher($routes, $context);

$parameters = $matcher->match('/hello');
```





