Silex-EsnGalaxySecurityServiceProvider
======================================
A Service Provider for Silex to authenticate through the [ESN Galaxy](http://galaxy.esn.org) CAS server.
With some *magic* extending classes could be used to authenticate with other CAS servers. Feel free to fork, edit and so.
## Instalation

```php
{
    "require": {
        "alejandroherr/silex-esngalaxysecurityserviceprovider": "dev-master"
    }
}
```
[More versions (if available)](https://packagist.org/packages/alejandroherr/silex-esngalaxysecurityserviceprovider)
## Configuration

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
            //'login_path' => '/login', DEFAULT VALUE
            'cas_server' => array(
                'base_url' => 'galaxy.esn.org',
                //'context' => 'cas', DEFAULT VALUE
                //'port' => '443', DEFAULT VALUE
                //'validation_path' => '/validation' DEFAULT VALUE
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

### Login and validation
You also must define routes for the login and validation paths. Here some examples:

```php
$app->match('/login', function () use ($app){		
	$errormsg = null;
	if($app['session']->has(SecurityContextInterface::AUTHENTICATION_ERROR)){
	    $error = $app['session']->get(SecurityContextInterface::AUTHENTICATION_ERROR);
	    $errormsg = $error->getMessage();
	}
    
	return $app['twig']->render(
	    'login.twig',
	    array(
	        'loginUrl' => $app['jasig_cas_client']->getLoginUrl($app['request']),
	        'error' => $errormsg
	    )
	);
});
$app->match('/validation', function() use ($app){});
```

## License
Released under the MIT license. See the LICENSE file for details.

#### Code Hard, Party Harder
