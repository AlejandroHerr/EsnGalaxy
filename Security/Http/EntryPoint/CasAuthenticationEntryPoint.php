<?php
namespace AlejandroHerr\Silex\EsnGalaxy\Security\Http\EntryPoint;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Http\EntryPoint\FormAuthenticationEntryPoint;

class CasAuthenticationEntryPoint extends FormAuthenticationEntryPoint
{
    public function __construct(HttpKernelInterface $kernel, HttpUtils $httpUtils, $loginPath = '/login', $useForward = false)
    {
        parent::__construct($kernel, $httpUtils, $loginPath, $useForward);
    }
}
