<?php

namespace PageAnalyzer;

use GuzzleHttp\Client;
use DiDom\Document;

class CheckUrl
{
    private Client $client;
    private $response;
    private Document $document;


    public function __construct($url)
    {
        $this->client = new Client(['verify' => false]);
        $this->response = $this->client->get($url);
        $this->document = new Document($url, true);
    }

    public function getStatusCodeUrl(): int
    {
        return $this->response->getStatusCode();
    }

    public function getAllCheckElements(): array
    {
        $maxStrLength = 254;

        return [
            'status' => substr($this->getStatusCodeUrl(),0, $maxStrLength),
            'h1' => substr($this->getH1element(),0, $maxStrLength),
            'title' => substr($this->getTitle(),0, $maxStrLength),
            'description' => substr($this->getDescription(),0, $maxStrLength),
        ];
    }

    public function getH1element(): string
    {
        $element = optional($this->document->first('h1'));
        return trim($element->text());
    }

    public function getTitle(): string
    {
        $element = optional($this->document->first('title'));
        return trim($element->text());
    }

    public function getDescription(): string
    {
        $element = optional($this->document->first('meta[name=description]'));
        return trim($element->content);
    }
}
