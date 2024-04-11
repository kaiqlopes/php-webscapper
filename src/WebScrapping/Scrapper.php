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
      $data = [];
      $xPath = new \DOMXPath($dom);
      $links = $xPath->query("//a[contains(@class, 'paper-card')]");
      $ids = $xPath->query("//div[contains(@class, 'volume-info')]");
      $linkIterator = 0;

      if($links->length > 0) {

          foreach ($links as $link) {

              $href = $link->getAttribute('href');
              $linkHtml = file_get_contents($href);

              $linkDom = new \DOMDocument();
              $linkDom->loadHTML($linkHtml);
              $linkXPath = new \DOMXPath($linkDom);

              $id = intval($ids->item($linkIterator)->nodeValue);
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
                      $author = trim($authorsSearch->item($i)->nodeValue);
                      if ($author !== "") {
                          $authors[] = $author;
                      }
                  }

                  $universitiesSearch = $linkXPath->query("//div[contains(@class, 'f-gray')]//li");
                  $universitiesQuantity = $universitiesSearch->length / 2;

                  for ($i = 0; $i < $universitiesQuantity; $i++) {
                      $university = trim($universitiesSearch->item($i)->nodeValue);
                      if ($university !== "") {
                          $universities[] = $university;
                      }
                  }

              } else {
                  $authorsSearch = $linkXPath->query("//div[contains(@class, 'row')]//ul[contains(@class, 'list')]//li");
                  $authorsQuantity = $authorsSearch->length / 2;

                  for ($i = 0; $i < $authorsQuantity; $i++) {
                      $author = trim($authorsSearch->item($i)->nodeValue);
                      if ($author !== "") {
                          $authors[] = $author;
                      }
                  }

                  $universitiesSearch = $linkXPath->query("//div[contains(@class, 'panel-body')]//div[contains(@class, 'form-group')]//li");

                  for ($i = 0; $i < $universitiesSearch->length; $i++) {
                      $university = trim($universitiesSearch->item($i)->nodeValue);
                      if ($university !== "") {
                          $universities[] = $university;
                      }
                  }
              }

              $universities_dict = [];

              foreach ($universities as $university) {
                  $number = intval(substr($university, 0, 2));
                  $name = substr($university, 2);
                  $universities_dict[$number] = $name;
              }

              $authorsWithUniversities = array_values(array_map(function($author) use ($universities_dict) {
                  $completeName = explode(" ", $author);
                  $universityNumber = $completeName[count($completeName) - 1];

                  if ($universityNumber == "Login") {
                      return new Person("UNKNOWN", "UNKNOWN");
                  } else {
                      $authorName = implode(" ", array_slice($completeName, 0, -1));
                      return new Person($authorName, $universities_dict[$universityNumber]);
                  }
              }, $authors));

              $data[] = new Paper($id, $title, $presentationType, $authorsWithUniversities);
          }
      } else {
          print_r("Nothing was found");
      }
      return $data;
  }
}
