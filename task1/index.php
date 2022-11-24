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
  require_once "./helpers/searchDOM.php";

  // Handle button press
  if (isset($_POST["submit"])) {
    $filePath = "./data/wo_for_parse.html";

    // Load target file
    $file = File::loadHTML($filePath);

    // Generate DOM
    $dom = File::generateDOM($file);

    // Search info in DOM
    $extracted = searchDOM($dom);

    // Save as result.csv
    File::writeCSV($extracted);
  }
  ?>
</body>

</html>