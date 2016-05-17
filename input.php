
<?php
    require_once('config.php');
        
    $valueAdded = $_POST['value_added'];
    $oldValue = file_get_contents($addBalanceFile);
    $myfile = fopen($addBalanceFile, "w+") or die("Unable to open file!");
    $newValue = $oldValue + $valueAdded;
    fwrite($myfile, $newValue);
    fclose($myfile);
    echo "<h2>" . $oldValue . " + " . $valueAdded . " = ". $newValue . " total manual donations added.</h2>";
?>