<?php
    $vote = $_REQUEST['vote'];

    // For a real poll, store votes in a database
    // For this example, let's increment counts in a text file
    $filename = "poll_results.txt";
    $content = file($filename);

    $array = explode("||", $content[0]);
    $coffee = $array[0];
    $tea = $array[1];
    $latte = $array[2];

    if ($vote == "coffee") {
        $coffee++;
    } elseif ($vote == "tea") {
        $tea++;
    } elseif ($vote == "latte") {
        $latte++;
    }

    $insertvote = $coffee . "||" . $tea . "||" . $latte;
    $fp = fopen($filename, "w");
    fwrite($fp, $insertvote);
    fclose($fp);

    echo "Coffee: " . $coffee . "<br>";
    echo "Tea: " . $tea . "<br>";
    echo "Latte: " . $latte . "<br>";
?>
