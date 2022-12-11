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

    public function __construct($url)
    {
        $this->client = new Client();
        $this->response = $this->client->request('GET', $url);

        $this->document = new Document($url, true);
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
        $h1 = $this->document->find('h1')[0]->text() ?? '';
        $title = $this->document->find('title')[0]->text() ?? '';
        $descriptionHTML = $this->document->find('meta[name=description]')[0];

        if ($descriptionHTML != null) {
            $description = $this->normalizeDescription($descriptionHTML->html()) ?? '';
        }

        $maxStrLengrth = 255;
        return [
            'h1' => substr($h1, 0, $maxStrLengrth), 
            'title' => substr($title, 0, $maxStrLengrth), 
            'description' => substr($description, 0, $maxStrLengrth)
        ];
    }

    public function getStatusCode()
    {
        return $this->response->getStatusCode();
    }
}

// $testdescr = $this->document->find('meta[name=description]')[0];
        // print_r($testdescr);