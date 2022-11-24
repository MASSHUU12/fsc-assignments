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
  require_once "./helpers/file.php";

  // Handle button press
  if (isset($_POST["submit"])) {
    $filePath = "./data/wo_for_parse.html";

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

    // Load target file
    $file = File::loadHTML($filePath);

    // Generate DOM
    $dom = File::generateDOM($file);

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
    File::writeCSV($extracted);
  }
  ?>
</body>

</html>