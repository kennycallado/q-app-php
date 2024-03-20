# q-app-php

## PHP

### Modules requered:

If you run the proyect without the provided docker image you should install intl module for php. I am not including it as require in `composer.json` for convenience with other commands.

**composer.json**
``` json
{
...
    "ext-intl": "*",
...
}
```

## routes

### rank action arguments:

1. auth
1. body
1. path params && query params


### query params:

``` url
http://localhost:8080/?var="asdfsadf"&foo={"baz":"bar"}
```

## Build

### notes:

``` bash
composer install --no-dev --optimize-autoloader

```

## Composer

``` bash
docker run --rm --interactive --tty \
  --volume .:/app \
  --user $(id -u):$(id -g) \
  composer dump-autoload

##

podman run --rm --interactive --tty \
  --volume .:/app \
  composer dump-autoload
```

### Install dependencies

``` bash
docker run --rm --interactive --tty \
  --volume .:/app \
  --user $(id -u):$(id -g) \
  composer install


##
podman run --rm --interactive --tty \
  --volume .:/app \
  composer install
```

## Testing

``` bash
podman run --rm -v .:/app php:8.2-cli-alpine /app/vendor/bin/phpunit /app/tests
```

## Formatting

``` bash
podman run --rm -v .:/app php:8.2-cli-alpine /app/vendor/bin/pretty-php /app/src
```
