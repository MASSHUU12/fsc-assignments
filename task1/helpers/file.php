<?php

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

      if (!$file) throw new Error("File opening failed");

      return $file;
    } else throw new Error("File does not exists");
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
    if (!@$dom->loadHTML($file)) throw new Error("HTML loading failed");

    return $dom;
  }

  /**
   * Writes data to CSV file
   */
  public static function writeCSV($arr): void
  {
    $fp = fopen("./result.csv", "w+");

    if ($fp) {
      echo "<br /><h3>Successfully generated result.csv file</h3>";

      // Write CSV to file
      fputcsv($fp, array_keys($arr));
      fputcsv($fp, array_values($arr));
    } else echo "An error occurred while generating the result.csv file";
  }
}
