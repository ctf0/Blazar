# Blazar

is a package that aims to help developers automate pre-rendering pages on the fly through utilizing [PhantomJs](phantomjs.org) which runs in the background when needed without adding any overhead to the server nor to the user experience.

# Installation

- install [PhantomJs](http://phantomjs.org/download.html)

- `composer require ctf0/blazar`

- add the service provider to `config/app.php`

```php
'providers' => [
    ctf0\Blazar\BlazarServiceProvider::class,
]
```

- publish the package assets

`php artisan vendor:publish --provider="ctf0\Blazar\BlazarServiceProvider"`

- add the middlewares to `app/Http/Kernel.php`

```php
protected $middlewareGroups = [
    // ...
    \ctf0\Blazar\Middleware\Blazar::class,
];

protected $routeMiddleware = [
    // ...
    'dont-pre-render' => \ctf0\Blazar\Middleware\DontPreRender::class,
];
```

- the package use caching through **Redis** to store the rendered results, so make sure to check the [docs](https://laravel.com/docs/5.4/redis) for installation & configuration.

# Config

**config/blazar.php**

```php
return [
    /*
     * phantomjs bin path
     */
    'phantom_path' => '',

    /*
     * phantomjs script path
     *
     * leave it empty to the use the one from the package
     */
    'script_path' => '',

    /*
     * phantomjs options
     */
    'options' => '--ignore-ssl-errors=true --ssl-protocol=any --disk-cache=false --debug=true 2>&1',

    /*
     * prerender the page only if the url has "?_escaped_fragment"
     */
    'bots_only' => false,

    /*
     * log the url when its processed by phantomjs
     */
    'debug' => true,
];
```

# Usage

- we use [Queues](https://laravel.com/docs/5.4/events#queued-event-listeners) to **pre-render the visited pages** in the background for more than one reason

    >- avoid latency when the page is being accessed for the first time.
    >- don't keep the user waiting in case `PhantomJs` took long to render the page or when something goes wrong.
    >- after `PhantomJs` has finished rendering, the page is cached to optimize server load even further.
    >- make your website **SEO friendly**, because instead of serving the normal pages that usually produce issues for crawlers, we are now serving the **pre-renderd version**. [ReadMore](#render-pages-automatically)
    >- even for websites with some traffic, we are still going to process each visited page without any problems.

#### # Render Pages Automatically

Atm in order to ***pre-render*** any page, it have to be visited first but if you want to make sure that all is working from day one, you can use the excellent package [laravel-link-checker](https://packagist.org/packages/spatie/laravel-link-checker) by **Spatie**

- which by simply running `php artisan link-checker:run` you will

    >- check which "url/link" on the website is not working.
    >- **pre-render** all pages at once.

#### # Flushing The Cache

- to avoid cluttering the cache we are clearing each user cache on logout but incase you want to clear the whole package cache, you can use

```bash
php artisan blazar:flush
```

#### # Bots Only

if you decided to pre-render the pages for bots only, no need to the run the queue as the page will remain busy **"stalled response"** until rendered by `PhantomJs`, which happens on the fly.

however because we are caching the result, so this will only happen once per page.

# Notes

#### # Why PhantomJs

I've tried both [usus](https://github.com/gajus/usus) & [puppeteer](https://github.com/GoogleChrome/puppeteer),

And my only take that both needs to run an instance of **Chrome**, while i wanted to keep the whole thing as hidden and as low-leveled as possible.

however if anyone knows how to get any to work as "PhantomJs", am all ears :ear: .

#### # Queues

the worker should only fires when <u>a url is visited & if this url is not cached</u>,
however if you have an unfinished old process, the queue will start processing pages on its own, so to fix that, simply restart the queue server `beanstalkd, redis, etc...`

```bash
# ex. using HomeBrew

brew services restart beanstalkd
```

#### # Auth

due to laravel refreshing the cookies values on each time the page gets ***visited/refreshed***, there is no way to attach the correct cookies to `PhantomJs` requests, which will cause issues with routes that use the **`auth` middleware**.

what happens is you will be able to login & visit any of the guarded pages for the first time "not cached yet", but from the second visit you will be redirected to the login page instead,

however we have a little trick to tackle that and the user status will persist across views, except each page he tries to visit will produce the same behavior,

so to solve that, simply add **`dont-pre-render` middleware** to those routes and everything will work as expected.

also make sure to add the middleware to the **"login & logout"** routes as well to avoid getting `CSRF Token Mismatch` for other users trying to login.

#### # More Reading
- https://vuejsdevelopers.com/2017/04/09/vue-laravel-fake-server-side-rendering/
- http://thelazylog.com/using-PhantomJs-to-serve-html-content-of-single-page-app/

# ToDo
- do some testing with "Angular, React, etc.."
