Silex-EsnGalaxySecurityServiceProvider
======================================
A Service Provider for Silex to authenticate through the [ESN Galaxy](http://galaxy.esn.org) CAS server.
With some *magic* extending classes could be used to authenticate with other CAS servers. Feel free to fork, edit and so.

**Warning:** This library is still work in progress. Meaning that some components may misbehave a little bit.
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
    'main' => array(  
        'esn_galaxy' => array(   
            'pattern' => '^/.*$',
            'anonymous' => true,
            'esn_galaxy' => array(
                'cas_server' => array(
                    'base_url' => 'galaxy.esn.org',
                    //'context' => 'cas',
                    //'port' => 443
                ),
                //'check_path' => '/validation',
                //'login_path' => '/login',
                // 'first_login_path' => '/welcome_user'
                'auth' => array(
                    'ES-BARC-UAB' => ['Local.webmaster' => 'ROLE_GOD'],
                    {{SECTION}} => [
                        {{GALAXY_ROLE}} => {{WEB_APP_ROLE}},
                        ....
                    ]
                )
        ),
        'logout' => array('logout_path' => '/logout'),
        'users' => $app->share(function() use ($app){
            return new AlejandroHerr\Silex\EsnGalaxy\Security\Core\User\SpawnedUserProvider();
        })
    )
);

$app['security.access_rules'] = array(
    array('^\/(?!login)', 'ROLE_USER'),
);
```

 ~~If you're using `DbalEsnGalaxyUserProvider` you need also to register the `DoctrineServiceProvider`. For example:~~

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
