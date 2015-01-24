<?php
namespace AlejandroHerr\Silex\EsnGalaxy\Cas\ResponseParser;

use Symfony\Component\DomCrawler\Crawler;

class EsnGalaxyResponseParser implements CasResponseParserInterface
{
    protected $username;
    protected $roles = array();
    protected $attributes;
    protected $tokenData;

    public function getTokenData(Crawler $response)
    {
        $this->setUsername($response);
        $this->setRoles($response);
        $this->setAttributes($response);

        $this->setTokenData();

        return $this->tokenData;
    }

    protected function setTokenData()
    {
        $this->tokenData['username'] = $this->username;
        $this->tokenData['attributes'] = $this->attributes;
        $this->tokenData['attributes']['roles'] = $this->roles;
    }

    protected function setUsername(Crawler $response)
    {
        $this->username = $response->filterXPath('//cas:user')->text();
    }
    protected function setRoles(Crawler $response)
    {
        $roles = $response->filterXPath('//cas:roles');

        if (count($roles)) {
            foreach ($roles as $role) {
                $this->roles[] = $role->nodeValue;
            }
        } else {
            $this->roles = null;
        }
    }
    protected function setAttributes(Crawler $response)
    {
        $attributes = $response->filterXPath('//cas:attributes')->children();

        if (count($attributes)) {
            foreach ($attributes as $attribute) {
                $nodeName = $this->cleanNodeName($attribute->nodeName);
                if (!(false === strpos($nodeName, 'roles')) || !(false === strpos($nodeName, 'attraStyle'))) {
                    continue;
                }
                $this->attributes[$nodeName] = $attribute->nodeValue;
            }
        } else {
            $this->attributes = null;
        }
    }

    protected function cleanNodeName($nodeName)
    {
        $casRegex = '/cas:([\w]+)/';
        if (1 !== preg_match($casRegex, $nodeName, $matches)) {
            return;
        }

        return $matches[1];
    }
}
