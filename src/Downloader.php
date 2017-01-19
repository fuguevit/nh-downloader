<?php

namespace Fuguevit\NHDownloader;

use Fuguevit\NHDownloader\Exception\GuzzleResultCodeError;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;

class Downloader
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var Client
     */
    protected $client;
    
    protected $baseWebUrl;

    public function __construct($id)
    {
        $this->id = $id;
        $this->client = new Client();
        $this->initNhParams();
    }

    protected function initNhParams()
    {
        $this->baseWebUrl = "https://nhentai.net";
    }

    public function start()
    {
        $res = $this->requestGalleryHtml();
        
        var_dump($this->extractImages((string) $res->getBody()));
    }

    protected function requestGalleryHtml()
    {
        $url = $this->baseWebUrl . '/g/' . $this->id;

        $res = $this->client->request('GET', $url);
        if ($res->getStatusCode() != 200) {
            throw new GuzzleResultCodeError;
        }
        
        return $res;
    }
    
    /**
     * @param string $html
     */
    protected function extractImages($html)
    {
        $crawler = new DomCrawler($html);

        return collect($crawler->filterXPath('//a/img')->extract(['data-src']))
            ->filter(function($url) {
                return strpos($url, "t.nhentai.net/galleries");
            })
            ->map(function($url) {
                return "https:".$url;
            });
    }

}