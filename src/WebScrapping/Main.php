<?php

namespace Chuva\Php\WebScrapping;

use Box\Spout\Common\Entity\Style\Color;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
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

    $writer = WriterEntityFactory::createXLSXWriter();
    $writer->openToFile(__DIR__ . '/../../assets/papersdata.xlsx');

    $style = (new StyleBuilder())
        ->setFontBold()
        ->setBackgroundColor(Color::GREEN)
        ->build();

    $cells = ["ID", "Title", "Type", "Author 1", "Author 1 Institution", "Author 2", "Author 2 Institution",
        "Author 3", "Author 3 Institution", "Author 4", "Author 4 Institution", "Author 5", "Author 5 Institution",
        "Author 6", "Author 6 Institution", "Author 7","Author 7 Institution", "Author 8", "Author 8 Institution","Author 9", "Author 9 Institution",
        "Author 10", "Author 10 Institution", "Author 11", "Author 11 Institution", "Author 12", "Author 12 Institution",
        "Author 13", "Author 13 Institution", "Author 14", "Author 14 Institution", "Author 15", "Author 15 Institution", "Author 16", "Author 16 Institution",];

    $headerRow = WriterEntityFactory::createRowFromArray($cells, $style);
    $writer->addRow($headerRow);

    foreach ($data as $paper) {
        $currentRow = WriterEntityFactory::createRowFromArray([
            $paper->id, $paper->title, $paper->type]);

        foreach ($paper->authors as $author) {
            $currentRow->addCell(WriterEntityFactory::createCell($author->name));
            $currentRow->addCell(WriterEntityFactory::createCell($author->institution));
        }

        $writer->addRow($currentRow);
    }

    print_r($data);

    $writer->close();
  }
}
