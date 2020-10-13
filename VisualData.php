<html>
<head>
    <link rel="stylesheet" href="FP_STYLE.css" type="text/css">
    <title>Bamazon - One Stop Shop</title>
</head>

<body>

<h1>
    Visual Data

</h1>

<h2>
    Please select product and type of analysis for display
</h2>

<div class="form" id="id0" style="width: 25%; height: 180px">
    <form name="genReview" action="<?php $_SERVER['PHP_SELF']?>" method="post">

        Product ID: <label> <input type="text" maxlength="100" name="pID" required></label>
        <br><br>
        <dl>
            <dd><label><input type="radio" name="Qtype" value="TotalReads">
                    Total number of readings<br></label>
            <dd><label><input type="radio" name="Qtype" value="TotalHelps">
                    Number of 'helpful' votes<br></label>
            <dd><label><input type="radio" name="Qtype" value="OVRrating">
                    Overall rating</label>
        </dl>
<div class="FormButtons" id="id1">
    <button name="submit" type="submit">Display Visual Data</button>
</div>
</div>
</form >

<?php
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

include("pChart/pChart/pData.class");
include("pChart/pChart/pChart.class");
include("pChart/pChart/pCache.class");

if(isset($_POST["submit"])) {
    $pID = $_POST["pID"];
    $DType = $_POST["Qtype"];
    $Dpoints = array();

    $DataSet = new pData;
    $DataSet->AddSerie();

    if($DType == 'TotalReads' ){
        $sql = "SELECT TOP 10 unixTimeReview, overallRead
                FROM reviews2
                WHERE asin = '$pID'
                ORDER BY unixTimeReview ASC 
                 ";

        if($result = sqlsrv_query($conn,$sql)){
            while ($row = sqlsrv_fetch_array($result,SQLSRV_FETCH_ASSOC)){
                array_push($Dpoints,$row["overallRead"] );
            }
        }
        $DataSet->SetSerieName("Total Number of Readings","Serie1");
    }

    if($DType == 'TotalHelps' ){
        $sql = "SELECT TOP 10 unixTimeReview, helped
                FROM reviews2
                WHERE asin = '$pID'
                ORDER BY unixTimeReview ASC 
                ";

        $result = sqlsrv_query($conn,$sql);
        while ($row = sqlsrv_fetch_array($result,SQLSRV_FETCH_ASSOC)){
            array_push($Dpoints,$row["helped"] );
        }
        $DataSet->SetSerieName("Total Number of Help Votes","Serie1");
    }
    if($DType == 'OVRrating' ){
        $sql = "SELECT TOP 10 unixTimeReview, overall
                FROM reviews2
                WHERE asin = '$pID'
                ORDER BY unixTimeReview ASC 
                ";

        $result = sqlsrv_query($conn,$sql);
        while ($row = sqlsrv_fetch_array($result,SQLSRV_FETCH_ASSOC)){
            array_push($Dpoints,$row["overall"] );
        }
        $DataArray = array_values($Dpoints);
        $DataSet->SetSerieName("Overall Rating of Review","Serie1");
    }


    $DataSet->AddPoint($Dpoints);
    $Test = new pChart(700,230);
    $Test->setFontProperties("pChart/Fonts/tahoma.ttf",10);
    $Test->setGraphArea(40,30,680,200);
    $Test->drawGraphArea(252,252,252);
    $Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,0,2);
    $Test->drawGrid(4,TRUE,230,230,230,255);
    $Test->drawLineGraph($DataSet->GetData(),$DataSet->GetDataDescription());
    $Test->drawPlotGraph($DataSet->GetData(),$DataSet->GetDataDescription(),3,2,255,255,255);
    $Test->setFontProperties("pChart/Fonts/tahoma.ttf",13);
    $Test->drawLegend(45,35,$DataSet->GetDataDescription(),255,255,255);
    $Test->setFontProperties("pChart/Fonts/tahoma.ttf",18);
    $Test->drawTitle(60,22,"Product '$pID' Results",50,50,50,585);
    $Test->Render("graph.png");


echo "<img src=\"graph.png\" alt=\"Requested Graph\" style=\" width: 100%; height: 100%; margin: 0 auto;display: block\">";

}

?>

<p>
    <a href="index.php" target="_self">Back To Main Screen</a>
</p>

</body>
</html>
