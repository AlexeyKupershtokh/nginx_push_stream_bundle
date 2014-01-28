<?php

namespace Alawar\NginxPushStreamBundle;

use Alawar\NginxPushStreamBundle\Filter\FilterInterface;
use Alawar\NginxPushStreamBundle\Http\SenderInterface;
use Alawar\NginxPushStreamBundle\IdGenerator\IdGeneratorInterface;

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
     * @var IdGeneratorInterface
     */
    protected $idGenerator = null;

    /**
     * @var SenderInterface
     */
    protected $sender = null;

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
     * @param IdGeneratorInterface $idGenerator
     */
    public function setIdGenerator(IdGeneratorInterface $idGenerator)
    {
        $this->idGenerator = $idGenerator;
    }

    /**
     * @param SenderInterface $sender
     */
    public function setSender(SenderInterface $sender)
    {
        $this->sender = $sender;
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

    public function send($token, $data, $type = null, $id = null)
    {
        if (!$this->sender) {
            return false;
        }

        $url = $this->getPubUrl($token);

        if ($id === null && $this->idGenerator) {
            $id = $this->idGenerator->generate();
        }

        $msg = array();
        $msg['token'] = $token;

        $headers = array();
        if ($id !== null) {
            $msg['id'] = $id;
            $headers['Event-ID'] = preg_replace('/\\r\\n/', '', $id);
        }

        if ($type !== null) {
            $msg['type'] = $type;
            $headers['Event-Type'] = preg_replace('/\\r\\n/', '', $type);
        }

        $headers['Content-Type'] = 'application/json';

        $msg['data'] = $data;

        $json = json_encode($msg);

        $body = $json . "\r\n";

        return $this->sender->send($url, $body, $headers);
    }
}
