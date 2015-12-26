# Dependency injection container

[![Build Status](https://img.shields.io/travis/php-lab/di/master.svg)](https://travis-ci.org/php-lab/di)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/php-lab/di.svg)](https://scrutinizer-ci.com/g/php-lab/di/)
[![Total Downloads](https://img.shields.io/packagist/dt/php-lab/di.svg)](https://packagist.org/packages/php-lab/di)
[![License](https://img.shields.io/packagist/l/php-lab/di.svg)](https://packagist.org/packages/php-lab/di)

PhpLab\Di requires PHP 7.

## Usage
```php
use PhpLab\Di\Container;

$app = new Container();

$app->pageIndexAction = function (Container $di) {
    return new \Page\Action\IndexAction($di->pageIndexResponder);
};
$app->pageIndexResponder = function (Container $di) {
    return new \Page\Responder\IndexResponder($di->pageIndexTemplate);
};
$app->pageIndexTemplate = function (Container $di) {
    return new \Page\Template\IndexTemplate($di['path.template']);
};

$app['path.root'] = __DIR__ . '/../..';
$app['path.template'] = $app['path.root'] . '/template/site';
```

## License
PhpLab\Di is licensed under the MIT license.
