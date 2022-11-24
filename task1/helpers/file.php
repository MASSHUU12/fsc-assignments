<?php

declare(strict_types=1);

define("ERR_FILE_OPENING", "File opening failed");
define("ERR_NO_FILE", "File does not exists");
define("ERR_HTML_LOADING", "HTML loading failed");
define("ERR_GENERATING_CSV", "There was a problem when generating the CSV file");

/**
 * Class for loading HTML files and writing to CSV files
 */
class File
{
  /**
   * Load HTML file as string
   */
  public static function loadHTML(string $path): string
  {
    if (file_exists($path)) {
      $file = file_get_contents($path);

      if (!$file) throw new Error(ERR_FILE_OPENING);

      return $file;
    } else throw new Error(ERR_NO_FILE);
  }

  /**
   * Convert string to HTML, or throw error on failure
   */
  public static function generateDOM(string $file): \DOMDocument
  {
    $dom = new DOMDocument();

    /**
     * The warning has been hidden because it does not change anything.
     * If everything went well, the HTML is returned, and if not,
     * the actual error is thrown thanks to if
     */
    if (!@$dom->loadHTML($file)) throw new Error(ERR_HTML_LOADING);

    return $dom;
  }

  /**
   * Writes data to CSV file
   */
  public static function writeCSV(array $arr): void
  {
    $fp = fopen("./result.csv", "w+");

    if ($fp) {
      // Write CSV to file
      if (!fputcsv($fp, array_keys($arr))) throw new Error(ERR_GENERATING_CSV);
      if (!fputcsv($fp, array_values($arr))) throw new Error(ERR_GENERATING_CSV);

      echo "<br /><h3>Successfully generated result.csv file</h3>";
    } else throw new Error(ERR_GENERATING_CSV);
  }
}
