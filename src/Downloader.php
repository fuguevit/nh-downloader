<?php

namespace Fuguevit\NHDownloader;

use Fuguevit\NHDownloader\Exception\GuzzleResultCodeError;
use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
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

    public function __construct($id, $proxy = null)
    {
        $this->id = $id;

        $clientOption = [
            RequestOptions::TIMEOUT => 10,
        ];

        if ($proxy) {
            $clientOption[RequestOptions::PROXY] = $proxy;
        }

        $this->client = new Client($clientOption);
        $this->initNhParams();
    }

    protected function initNhParams()
    {
        $this->baseWebUrl = 'https://nhentai.net';
    }

    public function start()
    {
        $res = $this->requestGalleryHtml();

        $images = ($this->extractImages((string) $res->getBody()));

        $this->downloadImages($images);
    }

    protected function requestGalleryHtml()
    {
        $url = $this->baseWebUrl.'/g/'.$this->id;

        $res = $this->client->request('GET', $url);
        if ($res->getStatusCode() != 200) {
            throw new GuzzleResultCodeError();
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
            ->filter(function ($url) {
                return strpos($url, 't.nhentai.net/galleries');
            })
            ->map(function ($url) {
                return 'https:'.str_replace('t.jpg', '.jpg', $url);
            });
    }

    /**
     * @param $links
     */
    protected function downloadImages($links)
    {
        $requests = $this->getImageRequests($links);

        $pool = new Pool($this->client, $requests, [
            'concurrency' => 10,
            'fulfilled'   => function ($response, $index) {
                $this->handleResponse($response, $index);
            },
            'rejected' => function ($response, $index) {
                // do nothing.
            },
        ]);

        $promise = $pool->promise();
        $promise->wait();
    }

    protected function getImageRequests($links)
    {
        foreach ($links as $key => $link) {
            yield new Request('Get', $link);
        }
    }

    protected function handleResponse($response, $index)
    {
        $dir = __DIR__.'/../storage/'.$this->id;
        if (!file_exists($dir)) {
            mkdir($dir);
        }
        $content = (string) $response->getBody();

        $name = sprintf('%04d', $index + 1);
        $file = $dir.'/'.$name.'.jpg';
        file_put_contents($file, $content);
    }
}
