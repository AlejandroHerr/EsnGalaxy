<?php
namespace AlejandroHerr\Silex\EsnGalaxy;

use AlejandroHerr\Silex\EsnGalaxy\Security\Core\Authentication\Provider\EsnGalaxyAuthenticationProvider;
use AlejandroHerr\Silex\EsnGalaxy\Security\Http\EntryPoint\CasAuthenticationEntryPoint;
use AlejandroHerr\Silex\EsnGalaxy\Security\Http\Firewall\EsnGalaxyAuthenticationListener;
use Silex\Application;
use Silex\ServiceProviderInterface;

class EsnGalaxyServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['security.authentication_listener.factory.esn_galaxy'] = $app->protect(function ($name, $options) use ($app) {
            if (!isset($app['security.authentication_listener.'.$name.'.esn_galaxy'])) {
                $app['security.authentication_listener.'.$name.'.esn_galaxy'] = $app['security.authentication_listener.esn_galaxy._proto']($name, $options);
            }
            if (!isset($app['security.authentication_provider.'.$name.'.esn_galaxy'])) {
                $app['security.authentication_provider.'.$name.'.esn_galaxy'] = $app['security.authentication_provider.esn_galaxy._proto']($name, $options);
            }
            $app['security.authentication_entry_point.'.$name.'.esn_galaxy'] = $app->share(function () use ($app, $options) {
                return new CasAuthenticationEntryPoint($app['security.http_utils'], $options);
            });

            return array(
                'security.authentication_provider.'.$name.'.esn_galaxy',
                'security.authentication_listener.'.$name.'.esn_galaxy',
                'security.authentication_entry_point.'.$name.'.esn_galaxy',
                'pre_auth',
            );
        });

        $app['security.authentication_listener.esn_galaxy._proto'] = $app->protect(function ($name, $options) use ($app) {
            return $app->share(function () use ($app, $name, $options) {
                if (!isset($options['cas_server']['base_url'])) {
                    $options['cas_server']['base_url'] = 'galaxy.esn.org';
                }
                $app['jasig_cas_client'] = $app->share(function () use ($options, $app) {
                    return new JasigClient($options['cas_server']);
                });
                if (!isset($app['security.authentication.success_handler.'.$name.'.esn_galaxy'])) {
                    $app['security.authentication.success_handler.'.$name.'.esn_galaxy'] = $app['security.authentication.success_handler._proto']($name, $options);
                }

                if (!isset($app['security.authentication.failure_handler.'.$name.'.esn_galaxy'])) {
                    $app['security.authentication.failure_handler.'.$name.'.esn_galaxy'] = $app['security.authentication.failure_handler._proto']($name, $options);
                }

                return new EsnGalaxyAuthenticationListener(
                    $app['jasig_cas_client'],
                    $app['security'],
                    $app['security.authentication_manager'],
                    $app['security.session_strategy'],
                    $app['security.http_utils'],
                    $name,
                    $app['security.authentication.success_handler.'.$name.'.esn_galaxy'],
                    $app['security.authentication.failure_handler.'.$name.'.esn_galaxy'],
                    $options,
                    $app['logger'],
                    $app['dispatcher']
                );
            });
        });

        $app['security.authentication_provider.esn_galaxy._proto'] = $app->protect(function ($name, $options) use ($app) {
            return $app->share(function () use ($app, $name, $options) {
                if (!isset($options['auth'])) {
                    $options['auth'] = [
                        '*' => [
                            'Local.activeMember' => 'ROLE_USER',
                            'Local.regularBoardMember' => 'ROLE_BOARD',
                        ],
                    ];
                }

                return new EsnGalaxyAuthenticationProvider(
                    $app['security.user_provider.'.$name],
                    $options['auth']
                );
            });
        });
    }

    public function boot(Application $app)
    {
    }
}
