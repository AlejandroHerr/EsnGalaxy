Silex-EsnGalaxySecurityServiceProvider
======================================

## CONFIGURATION

```php
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new SecurityServiceProvider());

$app->register(new AlejandroHerr\Silex\EsnGalaxy\EsnGalaxyServiceProvider());

$app['security.firewalls'] = array(
    'login' => array(
        'anonymous' => true,
        'pattern' => '^/login$'
    ),
    'main' => array(  
        'cas' => array(   
            'pattern' => '^/',
            'anonymous' => true,
            //'login_path' => '/login',
            'cas_server' => array(
                'base_url' => 'galaxy.esn.org',
                //'context' => 'cas',
                //'port' => '443',
                //'validation_path' => '/validation' 
            ),
            'auth' => array(
                'section' => 'ES-BARC-UAB'
            )
        ),
        'logout' => array('logout_path' => '/logout'),
        'users' => $app->share(function() use ($app){
            return new AlejandroHerr\Silex\EsnGalaxy\Security\Core\User\SpawnedUserProvider();
        })
    )
);
```

If you're using `DbalEsnGalaxyUserProvider` you need also to register the `DoctrineServiceProvider`. For example:
```php
$app['db.config'] = require_once ROOT . '/config/db.php';
$app->register(new DoctrineServiceProvider(),$app['db.config']);
```
