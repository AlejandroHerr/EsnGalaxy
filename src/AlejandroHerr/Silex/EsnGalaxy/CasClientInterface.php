<?php

namespace AlejandroHerr\Silex\EsnGalaxy;

use Symfony\Component\HttpFoundation\Request;

interface CasClientInterface
{
    public function getLoginUrl(Request $request);
    public function isValidationRequest(Request $request);
    public function validateTicket(Request $request);
}
