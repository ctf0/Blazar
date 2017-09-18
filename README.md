# Blazar

aims to help automate pre-rendering pages on the fly through utilizing [PhantomJs](phantomjs.org) which runs in the background when needed without adding any overhead to the server nor to the user experience.

## Installation

- install [PhantomJs](http://phantomjs.org/download.html)

- `composer require ctf0/blazar`

- (Laravel < 5.5) add the service provider to `config/app.php`

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

## Config
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

    /**
     *  clear user cache on logout
     */
    'clear_user_cache' => true
];
```

## Usage

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

- to clear the whole package cache, you can use

```bash
php artisan blazar:flush
```

or from within your app

```php
Artisan::call('blazar:flush');
```

#### # Bots Only

if you decided to pre-render the pages for bots only, no need to the run the queue as the page will remain busy **"stalled response"** until rendered by `PhantomJs`, which happens on the fly.

however because we are caching the result, so this will only happen once per page.

also note that we are saving the page cache equal to the url so even if you switched off the `bots_only` option, if the page is cached then we will always serve the cached result.

## Notes

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

as i dont know how to make laravel think that a page visited through phantomjs is the same as the current logged in user.

so trying to pre-render pages with **`auth` middleware** will be cashed as if the user was redirected to the home page or whatever you've set to **redirectTo** under `Constollers/Auth/LoginController.php` & `Middleware/RedirectIfAuthenticated.php`

so to solve that, simply add **`dont-pre-render` middleware** to those routes and everything will work as expected.
also make sure to add the same middleware to any route that needs fresh csrf-token for each user **"login, register, etc.."** to avoid getting `CSRF Token Mismatch` for other users trying to use those pages.

#### # More Reading
- https://vuejsdevelopers.com/2017/04/09/vue-laravel-fake-server-side-rendering/
- http://thelazylog.com/using-PhantomJs-to-serve-html-content-of-single-page-app/
