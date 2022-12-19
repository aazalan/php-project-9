<?php

namespace App;

use GuzzleHttp\Client;
use DiDom\Document;

require __DIR__ . '/../vendor/autoload.php';

class Check 
{
    private $client;
    private $response;
    private $document;
    private $defaultHTML = "<body></body>";

    public function __construct($url)
    {
        $this->client = new Client();
        $this->response = $this->client->request('GET', $url);
        $this->document = new Document();
        try {
            $this->document->loadHtmlFile($url);
        } catch (\Exception $e) {
            $this->document->loadHtml($this->defaultHTML);
        }
    }

    private function normalizeDescription($description)
    {
        $htmlDoc = new \DOMDocument();
        $htmlDoc->loadHTML($description);
        $tags = $htmlDoc->getElementsByTagName('meta');
        foreach($tags as $tag) {
            $content = $tag->getAttribute('content');
        }
        return $content;
    }

    public function getFullCheckInformation()
    {
        $h1HTML = $this->document->first('h1');
        if ($h1HTML !== null) {
            $h1 = $h1HTML->text();
        }

        $titleHTML = $this->document->first('title');
        if ($titleHTML !== null) {
            $title = $titleHTML->text();
        }

        $descriptionHTML = $this->document->first('meta[name=description]');
        if ($descriptionHTML !== null) {
            $description = $descriptionHTML->content;
        }

        $maxStrLengrth = 254;
        return [
            'h1' => substr($h1, 0, $maxStrLengrth) ?? '', 
            'title' => substr($title, 0, $maxStrLengrth) ?? '', 
            'description' => substr($description, 0, $maxStrLengrth) ?? ''
        ];
    }

    public function getStatusCode()
    {
        return $this->response->getStatusCode();
    }
}
