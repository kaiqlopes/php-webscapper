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
      $links = $xPath->query("//a[contains(@class, 'paper-card')]");
      $ids = $xPath->query("//div[contains(@class, 'volume-info')]");
      $linkIterator = 0;

      foreach ($links as $link) {

          $href = $link->getAttribute('href');
          $linkHtml = file_get_contents($href);

          $linkDom = new \DOMDocument();
          $linkDom->loadHTML($linkHtml);
          $linkXPath = new \DOMXPath($linkDom);

          $id = $ids->item($linkIterator)->nodeValue;
          $title = $xPath->query("//h4[contains(@class, 'paper-title')]")->item($linkIterator)->nodeValue;
          $presentationSearch = $xPath->query("//div[contains(@class, 'tags')][1]");
          $presentationType = $presentationSearch->item($linkIterator)->nodeValue;
          $linkIterator++;

          $authors = [];
          $universities = [];

          if ($presentationType == "Poster Presentation") {
              $authorsSearch = $linkXPath->query("//div[contains(@class, 'authors-wrapper')]//li");
              $authorsQuantity = $authorsSearch->length / 2;

              for ($i = 0; $i < $authorsQuantity; $i++) {
                  $authors[] = $authorsSearch->item($i)->nodeValue;
              }

              $universitiesSearch = $linkXPath->query("//div[contains(@class, 'f-gray')]//li");
              $universitiesQuantity = $universitiesSearch->length / 2;

              for ($i = 0; $i < $universitiesQuantity; $i++) {
                  $universities[] = $universitiesSearch->item($i)->nodeValue;
              }
          }
          else {
              $authorsSearch = $linkXPath->query("//div[contains(@class, 'row')]//ul[contains(@class, 'list')]//li");
              $authorsQuantity = $authorsSearch->length / 2;

              for ($i = 0; $i < $authorsQuantity; $i++) {
                  $authors[] = $authorsSearch->item($i)->nodeValue;
              }

              $universitiesSearch = $linkXPath->query("//div[contains(@class, 'panel-body')]//div[contains(@class, 'form-group')]//li");
              for ($i = 0; $i < $universitiesSearch->length; $i++) {
                  $universities[] = $universitiesSearch->item($i)->nodeValue;
              }
          }



          echo "\nId: " . $id . "\n";
          echo "Title: " . $title . "\n";
          echo "Presentation Type: " . $presentationType . "\n";
          echo "Authors: ";

          foreach ($authors as $author) {
              echo $author . ", ";
          }
          echo "\n";

          echo "Universities: ";
          foreach ($universities as $university) {
              echo $university . ", ";
          }
          echo "\n";

      }

      echo "\n";

      return [];
  }
}
