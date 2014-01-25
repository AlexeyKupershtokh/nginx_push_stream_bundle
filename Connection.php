<?php

namespace Alawar\NginxPushStreamBundle;

class Connection
{
    protected $pubUrl;
    protected $subUrls;
    protected $filters = array();

    public function __construct($pubUrl, $subUrls)
    {
        $this->pubUrl = $pubUrl;
        $this->subUrls = $subUrls;
    }

    public function addFilter($filter)
    {
        $this->filters[] = $filter;
    }

    public function filter($data)
    {
        foreach ($this->filters as $filter) {
            $data = $filter->filter($data);
        }
        return $data;
    }

    public function filterTokens(array $tokens)
    {
        $res = array();
        foreach ($tokens as $token) {
            $res[] = $this->filter($token);
        }
        return $res;
    }

    public function getSubUrls($tokens)
    {
        $filteredTokens = $this->filterTokens($tokens);
        $tokensString = join('/', $filteredTokens);
        $res = array();
        foreach ($this->subUrls as $type => $subUrl) {
            $res[$type] = str_replace('{tokens}', $tokensString, $subUrl);
        }
        return $res;
    }

    public function getPubUrl($token)
    {
        $filteredToken = $this->filter($token);
        $res = str_replace('{token}', $filteredToken, $this->pubUrl);
        return $res;
    }
}
