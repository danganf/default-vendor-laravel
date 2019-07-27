## Geral


1. Criei o service providers em `config/app.php`.

```php
'providers' => [

IntercaseDefault\LaravelDefaultServiceProvider::class,

],
```

2. Publique os pacotes.

```
php artisan vendor:publish --provider="IntercaseDefault\LaravelDefaultServiceProvider"
```