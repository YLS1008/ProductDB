<html>
<head>
    <link rel="stylesheet" href="FP_STYLE.css" type="text/css">
    <title>Bamazon - One Stop Shop</title>
</head>

<body>

<h1>
    Generate a New Review
</h1>

<h2>
    Set the parameters in the following form and generate a new review
</h2>

<div class="form" id="id0">
    <form name="genReview" action="<?php $_SERVER['PHP_SELF']?>" method="post">
        <div id ="id1" class="leftview">
            <br><br>
            User ID:
            <br><br>
            Product ID:
            <br><br>
            Number of tokens:
            <br><br>
            Probability:
            <br><br>
            Rating:
            <br><br>

        </div>

        <div id="id2" class="rightview">
            <br><br>
            <label>
                <input type="text" max="100" name="uID" required>
            </label>
            <br><br>
            <label>
                <input type="text" max="100" name="pID" required>
            </label>
            <br><br>
            <label>
                <input type="number" name="TokensNum" min="1" max="100" step="1" value="100">
            </label>
            <br><br>
            <label>
                <input type="number" name="probability" min="0" max="1" step="0.00001">
            </label>
            <br><br>
            <label>
                <input type="number" name="rating" min="1" max="5" step="1" value="1">
            </label>
        </div>
        <br>
</div>
<div class="FormButtons" id="id03">
    <button name="submit" type="submit">Generate Review</button>
    <button name="reset" type="reset">Clear Form</button>
</div>
</form >


<p>
    <a href="index.php" target="_self">Back To Main Screen</a>
</p>

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

if(isset($_POST["submit"])) {

    $safeUID = data_Escape($_POST["uID"]);
    $safePID = data_Escape($_POST["pID"]);
    $probability = $_POST["probability"];
    $rating = $_POST["rating"];
    $tokensNum = $_POST["TokensNum"];
    $revText = "";
    $it = 0;

    if($probability == 0){$probability = 1;}

    $sql = "SELECT *
            FROM corpus
            ";

    $result = sqlsrv_query($conn,$sql);
    while($it < $tokensNum){
        while ($row = sqlsrv_fetch_array($result,SQLSRV_FETCH_ASSOC)){
            $tokenProb = rand(0,10000)/10000;
            if($it < $tokensNum){

                if($probability > $tokenProb){
                    $revText = $revText. ' ' .$row['token'];
                    $it++;
                    }
                else{
                    continue;
                    }
            }
            else break;
        }
    }
    $unixTime = time();

    $sql = "INSERT INTO reviews2
            VALUES ('$safeUID','$safePID', '$revText','$unixTime', '$rating','0','0');";

    $result = sqlsrv_query($conn,$sql);

    if(!$result){
        echo "<p> Oops!Something went wrong, please check the input data and re-submit</p>";
        die(print_r(sqlsrv_errors(),true));
    }
    if($result){
        echo "<p>The Review was added to the database successfully </p> <p>Thank You!</p>";
    }
}

?>
</body>
</html>

