Aurex
===

Silex on steroids
----

![Status](https://img.shields.io/badge/Status-In%20Development-blue.svg)
[![Build Status](https://travis-ci.org/J7mbo/Aurex.svg?branch=master)](https://travis-ci.org/J7mbo/Aurex)

Aurex is a merge between the [Silex](http://silex.sensiolabs.org) micro-framework and the awesome [Auryn](https://github.com/rdlowrey/Auryn) 
dependency injector.

Aurex is useful for rapid application development with best practice in mind. If you know what you're doing with code, you'll love it.

It comes with the following already integrated:

  - Doctrine ORM with Entities / Repositories / Command Line / Fixtures
  - Symfony Forms that work with Doctrine entities
  - Monolog logging, YAML config parsing and caching
  - Working custom user provider, user entity and login form
  - Twig Templating Engine integration
  - Working firewall - currently, anonymous users can visit `/` and `/login`. Everything else is secured!
  - Controllers as services (each controller is an object with action methods)
  - Environment specific configuration files
  - "Modules" to group individual service provider integration and setup
  - Recursive auto-wiring dependency injection in every object from your controllers onward
  
If you know how to use Silex then you know how to use Aurex, the only differences being a different controller resolver 
to instantiate controllers and their dependencies recursively and service providers being setup within individual 
objects (modules).

You can use this as a standalone framework with which to write good, SOLID code, without using a service locator with 
which to pull in dependencies, or fork it to see how to get Silex working with the latest symfony components.
Effectively, this setup allows you to typehint for any object, anywhere, and it will automatically be resolved for you,
allowing you to focus on *what* you're creating and not *how* they are passed to eachother.
  
Aurex is setup with the latest Symfony 2.7 components, uses Silex 2, and will be maintained to stay up-to-date with 
Silex releases.

Example
---

We're going to go through the process of creating a page that displays some data retrieved via an API call. This involves:

- Creating a route
- Creating it's corresponding controller and template
- Using a third party library to make the API request in our controller
- Displaying the data retrieved from the API call in our template

This is a very rudimentary example but the point is to show how easy it is to get an object where you need in the application
by dependency injecting it without requiring any additional configuration / object registration.

Add a route in `Application/Config/routes.yml`:

```yaml
hello:
  pattern:    /hello
  controller: HelloController:indexAction
  template:   hello.html.twig
```

Create the controller as: `Application/Controller/HelloController` with the `indexAction` method. Extend `AbstractController`
if you want access to commonly used objects like security, the request, session or entity manager.

```php
namespace Aurex\Application\Controller;

class HelloController extends AbstractController
{
    public function indexAction()
    {
        /** -- SNIP **/
    }
}
```

Decide that you want to use the Guzzle Http library, for example, so require it with composer:

> require guzzlehttp/guzzle:~5.0

Typehint for GuzzleHttp\Client in your controller to have it automatically passed in for you with no extra configuration
(thanks, Auryn Injector). Then return the data to the template:

```php
public function indexAction(GuzzleHttp\Client $client)
{
    /** @var $json A json string from the httpbin.org containing the origin ip address **/
    $json   = $client->get('http://httpbin.org/ip');
    $data   = json_decode($json, true);
    $origin = $data['origin'];
    
    return [
        'origin' => $origin
    ];
}
```

Create your template as `web/templates/hello.html.twig`:

```html
    <p>
        Hello, you have access to the $origin variable in this Twig template.
        <br />
        It's value is: {{ origin }}.
    <p>
```

Visit `http://aurex.local/hello` to see your page.

This is basically the same as Silex / Symfony, yet it's much more streamlined and faster. 
**But what's the point of this**? Any object you need to access, like Guzzle's Http Client, you don't need to set up. 
You can just typehint for it in any of your controllers and it's passed in for you. This is also *recursive* - so if one 
object needs another object, typehint only for the first one and the dependency will also be created so that the first 
one can be injected. This is a really powerful tool to enable you to rapidly develop applications without worrying about 
how to gain access to the objects you need.

This is a very basic example. For other examples, including aliasing and more, see [EXAMPLE.md](EXAMPLE.md) (currently a WIP).

Installation
---

- You need [composer](https://getcomposer.org) installed
- Run `composer create-project j7mbo/aurex ./ 0.2.0`
- Create a virtual host (if using apache, else check the silex [web servers documentation](http://silex.sensiolabs.org/doc/web_servers.html)    ):

        <VirtualHost aurex.local:80>
            ServerName aurex.local
            ServerAlias aurex.local
        
            DocumentRoot "/path/to/aurex/web/"
            DirectoryIndex index.php
        
            <Directory "/path/to/aurex/web/">
                Options Indexes MultiViews FollowSymlinks
                Order allow,deny
                Require all granted
                AllowOverride All
                Allow from All
            </Directory>
        </VirtualHost>

- Set up your log file with permissions for both your user (for doctrine cli) and your webserver (for the application).
If all else fails, `chmod 777` it and feel bad about your life
- Create a database with the settings in [lib/Application/Config/dev.yml](lib/Application/Config/dev.yml) (feel free to
change)
- Create the database schema with `vendor/bin/doctrine orm:schema-tool:create`
- Run the fixtures to load the user roles and user into the database with: `vendor/bin/doctrine fixtures:load ./lib/Application/Model/Fixture --append`
- Login with `admin@aurex.com` and `password` - you can see how the fixture data is loaded from [lib/Application/Model/Fixture/LoadUsers.php](lib/Application/Model/Fixture/LoadUsers.php)
- Start writing awesome code in lib/Application as this isn't version controlled

Auryn Dependency Injector
---

[Auryn](https://github.com/rdlowrey/Auryn) is a recursive dependency injector. It uses [reflection](http://php.net/manual/en/intro.reflection.php), 
and subsequently caches those reflections, to read constructor and method signatures of objects and builds them in 
reverse-order for you. An object requiring multiple objects as dependencies can be created with `$injector->make('Object');` 
instead of calling `new Object(new Object2(new Object3))` etc.

The injector calls `make()` and `execute()` on controller constructors and methods so devs can typehint for any object 
they require in a controller and it will automatically be instantiated and passed in for them when the controller code 
is executed. This does away with the [service locator anti-pattern](http://blog.ploeh.dk/2010/02/03/ServiceLocatorisanAnti-Pattern/) 
and allows you to write SOLID, object-oriented code without worrying about how to wire your objects together or 
instantiate them in your controllers.

The following methods available to the auryn injector are used:
 
- [$injector->alias()](https://github.com/rdlowrey/Auryn#type-hint-aliasing) - Maps from interfaces or abstract classes 
to concrete implementations
- [$injector->share()](https://github.com/rdlowrey/Auryn#instance-sharing) - Shares a single instance around the 
application removing the need for a singleton
- [$injector->delegate()](https://github.com/rdlowrey/Auryn#instantiation-delegates) - To delegate an object's 
instantiation to a factory

The Auryn settings are located in [lib/Application/Config/dev.yml](lib/Application/Config/dev.yml) under the `auryn_module` key.

I highly recommend checking out the Auryn repository and playing around with it to see how it works.

The result is that you can typehint for any object, abstract or interface in your controller and it will be passed in for you!

Modules
----

The integration of individual service provider objects with their relevant configurations should be handled (wrapped) by 
a single object, or a "module", to keep each separate. Previously, one [large procedural file](https://github.com/J7mbo/silex-auth-skeleton/blob/master/src/App/Application.php) 
was used to setup all the service providers. Now service provider usage can adhere more to [SRP](http://en.wikipedia.org/wiki/Single_responsibility_principle).

The `ModuleLoader` loads any objects implementing `ModuleInterface`. Each Module is passed the `Aurex` application object
which contains the configuration and auryn injector objects, which you can then use to set up your individual service provider.

Modules are loaded in the order that [lib/Application/Config/global.yml](lib/Application/Config/global.yml) displays
them. To load a custom module you have just created, add the fully qualified class name (including namespace) and the
loader will load the module provided in the specified order.

Use modules to integrate other service providers or other objects that will act on the `Silex\Application` object. Each
module can also interact with the Auryn injector to share, delegate or perform whatever other functionality is required
to make your object work throughout the rest of the application.

For an example of a module, see the [RoutingModule](lib/Framework/Module/Modules/RoutingModule/RoutingModule.php). This
file is responsible for setting up routes according to the (already parsed) routes.yml file.

Bootstrapping
----

In the event you need to do any custom application bootstrapping after the [Aurex one](web/index.php) has executed, you
can modify the [lib/Application/index.php](lib/Application/index.php), as this file is included by the former before
`Silex\Application::run()` is called.

Templates
----

Twig templates are located in [web/templates](web/templates). If you specify a `template` key as seen in `routes.yml`,
your controller only needs to return an array of data and it will be passed to the template. If you omit the `template`
key, you can typehint for `\Twig_Environment` in your controller and call `::render()` with the relevant parameters to
render your template. The former method is purely for convenience.

To access the **user** object within templates, Silex provides `{{ app['security.token_storage'].token.user }}.

Environments
----

Usually Applications require different environment setups like "dev" and "live". Aurex comes with a default [DevEnvironment](lib/Framework/Environment/DevEnvironment.php) 
which sets some xdebug settings if available. The environment is built by an [EnvironmentFactory](lib/Framework/EnvironmentFactory).

You can create your own custom environment by implementing `EnvironmentInterface` and running whatever environment-specific
logic you require in the `perform(Aurex $aurex)` method. The file `global.yml` contains settings to load your own
environment files.

Config Caching
----

The annoying thing about configuration parsing (especially with YAML) is that, on every request, the data is read from 
the files before the application is booted. As a result there is an I/O constraint and this data should be cached if at 
all possible. If you have [memcached](http://php.net/manual/en/book.memcached.php) installed, the `ParserCacherFactory`
stores parsed configuration data in `memcached` to remove the I/O overhead.

As a result, if you add / remove configuration variables from any configuration files and you have a caching
implementation in effect, (currently restricted to memcached), you will need to restart memcached or else manually
remove the configuration keys (currently the configuration file paths) to have the new data used instead.
