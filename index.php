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
    One Stop Shop for all your shopping needs
</h2>

<p>
    The following system will allow you to upload review data, add new reviews and query for stats regarding reviews
</p>


<img src="Shopping.png" alt="Shopping Frenzy" style=" width: 300px; height: 300px; margin: 0 auto;display: block">


<p>
    <a href="LoadScreen.php" target="_self">To Upload Data Files</a>
</p>

<p>
    <a href="AddReview.php" target="_self">To Add a Review</a>
</p>

<p>
    <a href="VisualData.php" target="_self">To Display Visual Data</a>
</p>
<br>

<?php

echo "<table class='center' border =\"2\">";
echo "<tr><th colspan='4'>General Data</th></tr>";
echo "<tr><th>Highest Amount of Reviews</th>
        <th>Highest Sum of Helpful Ratings</th>
            <th>Highest Average Ratio</th>
                <th>Highest Number of Relations</th></tr>";

$server = "tcp:techniondbcourse01.database.windows.net,1433";
$user = "h0yonatan";
$pass = "Qwerty12!";
$database = "h0yonatan";
$c = array("Database" => $database, "UID" => $user, "PWD" => $pass);
sqlsrv_configure('WarningsReturnAsErrors', 0);
$conn = sqlsrv_connect($server, $c);
if($conn === false)
{
    echo "error";
    die(print_r(sqlsrv_errors(), true));
}

$sql = "SELECT TOP 1 reviewrID
        FROM (SELECT reviewrID, COUNT(unixTimeReview) AS revCount
        FROM reviews2
        GROUP BY reviewrID
        )  T1
        ORDER BY revCount DESC, reviewrID ASC 
        ";

$result = sqlsrv_query($conn, $sql);

$row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
$data1 = $row['reviewrID'];

$sql = "SELECT TOP 1 reviewrID
        FROM (SELECT reviewrID, SUM(helped) AS helpCount
              FROM reviews2
              GROUP BY reviewrID
              )T1
         ORDER BY helpCount DESC, reviewrID ASC
        ";

$result = sqlsrv_query($conn, $sql);

$row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
$data2 = $row['reviewrID'];


$sql = "SELECT TOP 1 T3.reviewrID
        FROM (SELECT T1.reviewrID, AVG(T1.ratio) as ratioAvg
              FROM (SELECT reviewrID,unixTimeReview, helped/overallRead AS ratio
                    FROM reviews2
                    WHERE overallRead !=0
                                  
              )T1
              RIGHT JOIN 
              (SELECT reviewrID, unixTimeReview
               FROM reviews2
              )T2
                ON T1.reviewrID = T2.reviewrID AND T1.unixTimeReview = T2.unixTimeReview
              GROUP BY T1.reviewrID
              )T3
        ORDER BY T3.ratioAvg DESC, T3.reviewrID ASC 
       ";

$result = sqlsrv_query($conn, $sql);
$row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
$data3 = $row['reviewrID'];

$sql = "SELECT TOP 1 referenceAsin
        FROM (SELECT referenceAsin, COUNT(relatedAsin) AS relCount
              FROM relations
              GROUP BY referenceAsin
             )T1
        ORDER BY relCount DESC, referenceAsin ASC 
";

$result = sqlsrv_query($conn, $sql);
$row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
$data4 = $row['referenceAsin'];

echo "<tr><td>$data1</td><td>$data2</td><td>$data3</td><td>$data4</td></tr>";

echo "</table>";

?>
</body>
</html>
