<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Parser</title>
</head>

<body style="background-color: #23272e;color: #4bb6a9;">
  <form action="#" method="post">
    <button type="submit" name="submit">Parse</button>
  </form>

  <?php
  // Handle button press
  if (isset($_POST["submit"])) {
    $filePath = "./wo_for_parse.html";

    $extracted = [
      "Tracking Number" => null,
      "PO Number" => null,
      "Scheduled Date" => null,
      "Customer" => null,
      "Trade" => null,
      "NTE" => null,
      "Store ID" => null,
      "Street" => null,
      "Town" => null,
      "State" => null,
      "ZIP Code" => null,
      "Phone" => null
    ];

    if (file_exists($filePath)) {
      // Load target file
      $file = file_get_contents($filePath);

      if (!$file) throw new Error("File opening failed");

      $dom = new DOMDocument();

      /**
       * Convert file from string to HTML, or throw error on failure
       *
       * The warning has been hidden because it does not change anything.
       * If everything went well, the HTML is returned, and if not,
       * the actual error is thrown thanks to if
       */
      if (!@$dom->loadHTML($file)) throw new Error("HTML loading failed");

      /** Search for data */

      $extracted["Customer"] = trim($dom->getElementById("customer")->nodeValue);
      $extracted["Tracking Number"] = $dom->getElementById("wo_number")->nodeValue;
      $extracted["PO Number"] = $dom->getElementById("po_number")->nodeValue;
      $extracted["Trade"] = $dom->getElementById("trade")->nodeValue;
      $extracted["Store ID"] = $dom->getElementById("location_name")->nodeValue;
      $extracted["Town"] = $dom->getElementById("store_id")->nodeValue;

      // Get address
      $address = $dom->getElementById("location_address")->nodeValue;

      // Replace spaces, blank characters, etc. with a single space
      $cleanAddress = trim(preg_replace("/\s+/", " ", $address));
      $addressArr = explode(" ", $cleanAddress);

      // Get State from address
      $extracted["State"] = $addressArr[sizeof($addressArr) - 2];

      // Get zip code from address
      $extracted["ZIP Code"] = end($addressArr);

      // Convert to float
      $extracted["NTE"] = floatval(preg_replace("/[^0-9]/", "", $dom->getElementById("nte")->nodeValue));
      // Convert to float
      $extracted["Phone"] = floatval(preg_replace("/[^0-9]/", "", $dom->getElementById("location_phone")->nodeValue));

      // Get Street from address
      $townIndex = array_search($extracted["Town"], $addressArr);

      // Loop through the address until the city is met
      for ($i = 0; $i < $townIndex; $i++) {
        $extracted["Street"] .= ' ' . $addressArr[$i];
      }

      try {
        // Replace spaces, blank characters, etc. with a single space
        $cleanDate = trim(preg_replace("/\s+/", " ", $dom->getElementById("scheduled_date")->nodeValue));

        // Transform string into an array
        $dArr = preg_split("/ |,/", $cleanDate);
        $formattedDate = $dArr[3] . '-' . date("m", strtotime($dArr[0])) . '-' . $dArr[1] . ' ' . $dArr[4] . ' ' . $dArr[5];

        $extracted["Scheduled Date"] = $formattedDate;
      } catch (\Throwable $th) {
        // echo $th;
        $extracted["scheduleDate"] = null;
      }

      /** Save as .csv */

      // Open/create file
      $fp = fopen("./result.csv", "w+");

      if ($fp) {
        echo "<br /><h3>Successfully generated result.csv file</h3>";

        // Write CSV to file
        fputcsv($fp, array_keys($extracted));
        fputcsv($fp, array_values($extracted));
      } else echo "An error occurred while generating the result.csv file";
    }
  }
  ?>
</body>

</html>