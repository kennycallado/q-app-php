# q-app-php


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
podman run --rm -v .:/app php:8.2-cli /app/vendor/bin/phpunit /app/tests
```
