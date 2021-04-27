Silex-EsnGalaxySecurityServiceProvider
======================================
A Service Provider for Silex to authenticate through the [ESN Galaxy](http://galaxy.esn.org) CAS server.
With some *magic* extending classes could be used to authenticate with other CAS servers. Feel free to fork, edit and so.

**Warning:** This library is still work in progress. Meaning that some components may misbehave a little bit.
## Installation

```php
{
    "require": {
        "alejandroherr/silex-esngalaxysecurityserviceprovider": "0.1"
    }
}
```
[More versions (if available)](https://packagist.org/packages/alejandroherr/silex-esngalaxysecurityserviceprovider)
## Configuration
The comment lines are showing its default value. Only overrider if you need it.
```php
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new SecurityServiceProvider());

$app->register(new AlejandroHerr\Silex\EsnGalaxy\EsnGalaxyServiceProvider());

$app['security.firewalls'] = [
    'main' => [  
        'esn_galaxy' => array(   
            'pattern' => '^/.*$',
            'anonymous' => true,
            'esn_galaxy' => [
                'cas_server' => [
                    //'base_url' => 'galaxy.esn.org',
                    //'context' => 'cas',
                    //'port' => 443
                    //'login_path' => '/login',
                    //'validation_path' => '/serviceValidate'
                ],
                //'check_path' => '/cas/validation',
                //'login_path' => '/login',
                // 'first_login_path' => '/welcome_user'
                'auth' => [
                    //'*' => [
                    //        'Local.activeMember' => 'ROLE_USER',
                    //        'Local.regularBoardMember' => 'ROLE_BOARD',
                    //    ]
                    //]
                ]
            ]
        ],
        'logout' => ['logout_path' => '/logout'], //if you want a logout
        'users' => $app->share(function() use ($app){
            return new Your\UserProvider();
        })
    ]
];

$app['security.access_rules'] = array(
    array('^\/(?!login)', 'ROLE_USER'),
);
```
### first_login_path
By adding a `first_login_path` (actually it could be a path or a route), when a logs in for the first time and a new user is created will be redirected to `first_login_path`.

It could be useful if your application permanent and you want them to provide further information, or if the user are spawned every time to give them some information (or just saying hi).
### Auth
The auth option controls which galaxy-roles from which section can access to the site. It's an array with the following strcture:
```php
'auth' => [
    'section1' => [
        'galaxy_role1' => 'app_role1',
        'galaxy_role2' => 'app_role2',
    ],
    'section2' => [
        'galaxy_role1' => 'app_role1',
        'galaxy_role3' => 'app_role2',
    ]
]
```

If section is `*` means 'any section'. Remember that **it can be a regex**. But to tell the Provider it's a regex it must start by `/`.

By default the configuration is:
```php
'auth' => [
    '*' => [
        'Local.activeMember' => 'ROLE_USER',
        'Local.regularBoardMember' => 'ROLE_BOARD',
    ]
]
```
#### Examples
##### Allowing only a country
```php
'auth' => [
    '/^ES/' => [
        'Local.activeMember' => 'ROLE_USER',
        'Local.regularBoardMember' => 'ROLE_BOARD',
    ]
]
```
##### Allowing only a National Board
```php
'auth' => [
    '/^ES/' => [
        'National.regularBoardMember' => 'ROLE_USER',
    ]
]
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
