<?php

namespace Chuva\Php\WebScrapping;

use Chuva\Php\WebScrapping\Entity\Paper;
use Chuva\Php\WebScrapping\Entity\Person;

/**
 * Does the scrapping of a webpage.
 */
class Scrapper {

  /**
   * Loads paper information from the HTML and returns the array with the data.
   */
  public function scrap(\DOMDocument $dom): array {
      $xPath = new \DOMXPath($dom);
      $query = "//a[contains(@class, 'paper-card')]";
      $links = $xPath->query($query);
      $ids = $xPath->query("//div[contains(@class, 'volume-info')]");
      $linkIterator = 0;

      $href = $links->item(0)->getAttribute('href');
      $linkHtml = file_get_contents($href);

      $linkDom = new \DOMDocument();
      $linkDom->loadHTML($linkHtml);
      $linkXPath = new \DOMXPath($linkDom);

      $id = $ids->item($linkIterator)->nodeValue;
      $title = $linkDom->getElementsByTagName('h2')->item(1)->nodeValue;
      $presentationType = $linkDom->getElementsByTagName('strong')->item(2)->nodeValue;

      $duplicatedAuthors = $linkXPath->query("//div[contains(@class, 'authors-wrapper')]//abbr");

      $authorsQuantity = $duplicatedAuthors->length / 2;
      $authors = [];

      for($i = 0; $i < $authorsQuantity; $i++) {
          $authors[] = $duplicatedAuthors->item($i)->nodeValue;
      }

      echo "\nId: " . $id . "\n";
      echo "Title: " . $title . "\n";
      echo "Presentation Type: " . $presentationType . "\n";
      echo "Authors: ";
      foreach ($authors as $author) {
          echo $author . ", ";
      }

      echo "\n";
      /*if ($links->length > 0) {

          foreach ($links as $link) {

          }

      }
      else {
          echo "Nothing was found";
      }*/

      return [];
  }

}
