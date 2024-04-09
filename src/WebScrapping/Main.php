<?php

namespace Chuva\Php\WebScrapping;

use DOMDocument;

/**
 * Runner for the Webscrapping exercice.
 */
class Main {

  /**
   * Main runner, instantiates a Scrapper and runs.
   */
  public static function run(): void {
    libxml_use_internal_errors(true);

    $dom = new \DOMDocument('1.0', 'utf-8');
      $dom->loadHTMLFile(__DIR__ . '/../../assets/origin.html');

    $data = (new Scrapper())->scrap($dom);

      print_r($data);
  }

}
