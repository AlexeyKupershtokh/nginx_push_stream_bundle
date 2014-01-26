<?php

namespace Alawar\NginxPushStreamBundle;

use Alawar\NginxPushStreamBundle\Filter\FilterInterface;

class Connection
{
    /**
     * @var string
     */
    protected $pubUrl;

    /**
     * @var array
     */
    protected $subUrls;

    /**
     * @var FilterInterface[]
     */
    protected $filters = array();

    /**
     * @param $pubUrl string
     * @param $subUrls array
     */
    public function __construct($pubUrl, $subUrls)
    {
        $this->pubUrl = $pubUrl;
        $this->subUrls = $subUrls;
    }

    /**
     * @param FilterInterface $filter
     */
    public function addFilter(FilterInterface $filter)
    {
        $this->filters[] = $filter;
    }

    /**
     * @param $data string
     * @return string
     */
    public function filter($data)
    {
        foreach ($this->filters as $filter) {
            $data = $filter->filter($data);
        }
        return $data;
    }

    /**
     * @param array $tokens
     * @return array
     */
    public function filterTokens(array $tokens)
    {
        $res = array();
        foreach ($tokens as $token) {
            $res[] = $this->filter($token);
        }
        return $res;
    }

    /**
     * @param array $tokens
     * @return array
     */
    public function getSubUrls(array $tokens)
    {
        $filteredTokens = $this->filterTokens($tokens);
        $tokensString = join('/', $filteredTokens);
        $res = array();
        foreach ($this->subUrls as $type => $subUrl) {
            $res[$type] = str_replace('{tokens}', $tokensString, $subUrl);
        }
        return $res;
    }

    /**
     * @param $token string
     * @return string
     */
    public function getPubUrl($token)
    {
        $filteredToken = $this->filter($token);
        $res = str_replace('{token}', $filteredToken, $this->pubUrl);
        return $res;
    }
}
