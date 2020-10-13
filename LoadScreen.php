<html>
<head>
    <link rel="stylesheet" href="FP_STYLE.css" type="text/css">
    <title>Bamazon - One Stop Shop</title>
</head>

<body>

<h1>
    Baaamazon
</h1>

<h2>
   Load Files
</h2>
<p>
    Press on the relevant button in order to upload data from a specific file
</p>

<div class="form" style="height: 100px;">
    <h2>
        Reviews Data
    </h2>
    <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="POST" enctype="multipart/form-data">
        <input name="csv" type="file" id="csv" />
        <input type="submit" name="submit1" value="submit" />
    </form>
</div>
<br>
<div class="form" style="height: 100px;">
    <h2>
        Relations Data
    </h2>
    <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="POST" enctype="multipart/form-data">
        <input name="csv" type="file" id="csv" />
        <input type="submit" name="submit2" value="submit" />
    </form>
</div>
<br>
<div class="form" style="height: 100px;">
    <h2>
        Corpus Data
    </h2>
    <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="POST" enctype="multipart/form-data">
        <input name="csv" type="file" id="csv" />
        <input type="submit" name="submit3" value="submit" />
    </form>
</div>
<br>


<?php

function data_Escape($data){
    $safedata = filter_var($data, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW );
    $safedata = filter_var($safedata, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_HIGH );
    $safedata = filter_var($safedata, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_AMP );
    $safedata = addslashes($safedata);

    return $safedata;
}

$server = "tcp:techniondbcourse01.database.windows.net,1433";
$user = "h0yonatan";
$pass = "Qwerty12!";
$database = "h0yonatan";
$c = array("Database" => $database, "UID" => $user, "PWD" => $pass);
sqlsrv_configure('WarningsReturnAsErrors', 0);
$conn = sqlsrv_connect($server, $c);
if ($conn === false) {

    echo "error";
    die(print_r(sqlsrv_errors(), true));
}
if(isset($_POST["submit1"])) {
    $file = $_FILES['csv']['tmp_name'];
    if (($handle = fopen($file, "r")) !== FALSE) {
        $AddedCount = 0;
        $TotalCount = 0;

        while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {


            $saferevID = data_Escape($data[0]);
            $safeAsin = data_Escape($data[1]);
            $safeText = data_Escape($data[2]);
            $safeOverall = intval($data[3]);

            $sql = "INSERT INTO reviews2 
                    VALUES ('$saferevID','$safeAsin','$safeText','". $data[4] ."',
                                '$safeOverall','" . $data[5] . "','" . $data[6] . "')";

            $result = sqlsrv_query($conn, $sql);


            if ($result == FALSE) {
                $TotalCount++;
                continue;
            }
            else {
                $AddedCount++;
                $TotalCount++;
                continue;
            }

        }
        --$TotalCount; /*assuming all csv files will have a header line*/
        echo "<p>$AddedCount Reviews out of $TotalCount were added to the database successfully</p>";

        fclose($handle);

    }
}
if(isset($_POST["submit2"])) {
    $file = $_FILES['csv']['tmp_name'];
    if (($handle = fopen($file, "r")) !== FALSE) {
        $AddedCount = 0;
        $TotalCount = 0;


        while (($data = fgetcsv($handle, 400, ",")) !== FALSE) {
            $safeRefAsin = data_Escape($data[0]);
            $safeRelatedAsin = data_Escape($data[1]);
            $safeType = data_Escape($data[2]);

            $sql = "INSERT INTO relations 
                    VALUES ('$safeRefAsin','$safeRelatedAsin', '$safeType')";

            $result = sqlsrv_query($conn, $sql);

            if ($result == FALSE) {
                $TotalCount++;
                 continue;
            } else {
                $AddedCount++;
                $TotalCount++;
                continue;
            }
        }
        $TotalCount--; /*assuming all csv files will have a header line*/
        echo "<p>$AddedCount Relations out of $TotalCount were added to the database successfully</p>";

        fclose($handle);

    }
}
if(isset($_POST["submit3"])) {
    $file = $_FILES['csv']['tmp_name'];
    if (($handle = fopen($file, "r")) !== FALSE) {
        $AddedCount = 0;
        $TotalCount = 0;
        $limit = $_POST["limit"];
        $startTime = microtime(1);
        $runTime = 0;
        while (($data = fgetcsv($handle, 400, ",")) !== FALSE) {

            $safeWord = data_Escape($data[0]);

            $sql = "INSERT INTO corpus 
                    VALUES ('$safeWord')";

            $result = sqlsrv_query($conn, $sql);

            if ($result == FALSE) {
                $TotalCount++;
                continue;
            } else {
                $AddedCount++;
                $TotalCount++;
                continue;
            }


        }

        echo "<p>$AddedCount Words out of $TotalCount were added to the database successfully</p>";


        fclose($handle);

    }
}
?>

<p>
    <a href="index.php" target="_self">Back To Main Screen</a>
</p>


</body>
