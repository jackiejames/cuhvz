<?php require('includes/config.php');

// if not logged in redirect to login page
if(!$user->is_logged_in()){ header('Location: login.php'); } 

// define page title
$title = 'HVZ CU BOULDER';

// include header template
//require('layout/header.php');
?>

<!-- START HEADER -->

<!DOCTYPE html>
<html lang="en">
<head>

  <!-- Basic Page Needs
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <meta charset="utf-8">

  <title><?php if(isset($title)){ echo $title; }?></title>

  <meta name="description" content="">
  <meta name="author" content="">

  <!-- Mobile Specific Metas
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- FONT
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <link href="//fonts.googleapis.com/css?family=Raleway:400,300,600" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Anton" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,700,900|Arimo:400,700|Roboto:400,700,900" rel="stylesheet">

  <!-- CSS
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <link rel="stylesheet" href="css/normalize.css">
  <link rel="stylesheet" href="css/skeleton.css">
  <link rel="stylesheet" href="css/custom.css">

  <!-- <link href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet"> -->

  <!-- Favicon
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <link rel="icon" type="image/png" href="images/favicon.png">
  
  <!-- FB Meta Tags (Attempt by Philip) -->
  <meta property="og:site_name"     content="HVZ CU BOULDER" />
  <meta property="og:type"          content="website" />
  <meta property="og:image"         content="http://www.cuhvz.com/layout/WeeklongPromo.jpg" />
  <meta property="og:description"   content="Can you survive a week in the zombie apocalypse?" />
  <meta property="og:url"           content="http://www.cuhvz.com" />
  <meta property="og:title"         content="HVZ CU BOULDER" />


  <!-- Google Analytics
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

      ga('create', 'UA-93715393-1', 'auto');
      ga('send', 'pageview');
   </script>


  <!-- SANDBOX -->

  <script src="js/jquery-3.1.1.min.js"></script>

  <script src="js/checkbox.js"></script>


</head>
<body>


<!-- END HEADER -->

<!-- START KILLHUMAN.PHP -->

<?php

// DATABASE CONNECTION INFORMATION
$host = "localhost";
$user = "user";
$passwd = "password";
$dbname = "hvz";
$cxn = mysqli_connect($host,$user,$passwd,$dbname) or die ("could not connect to server");

$zombieFeeder = $_SESSION['username'];

// CHECKS IF HEX MATCHES SOMETHING IN DATABASE AND RETURNS THE VICTIM'S EMAIL
function findVictim($cxn, $hex)
{
    $query_rng = "SELECT * FROM members";
    $result_rng = mysqli_query($cxn,$query_rng) or die ("could not execute query_rng");
    $victim = "none";

    if(!empty($result_rng))
    {
        while ($row_rng = mysqli_fetch_array($result_rng))
        {
            $tempUsername = $row_rng['username'];
            $tempHex = $row_rng['UserHex'];
            if(strcmp($hex, $tempHex) == 0)
            {
                $victim = $tempUsername;
            }
        }
    }
    
    return $victim;
}

// REGISTERS A KILL FOR A HUMAN
// Takes victim's email address
function regKill($cxn, $victim)
{
    // CHECK STATUS
    $query_status = "SELECT * FROM members WHERE username='$victim'";
    $result_status = mysqli_query($cxn,$query_status) or die ("could not execute query_status");
    $row_status = mysqli_fetch_array($result_status);
    $status = $row_status['status'];
    
    if(strcmp($status, "human") == 0)
    {
        $query_updateKill = "UPDATE members SET status='human' WHERE username='$victim'"; // CHANGE BACK TO 'ZOMBIE' AFTER TESTING
        $result_updateKill = mysqli_query($cxn,$query_updateKill) or die ("could not execute query_updateKill");
        return TRUE;
    }
    else
    {
        echo "The system does not recognize this person as a human. Check with an admin if this seems to be incorrect.";
        return FALSE;
    }
}

?>

<!-- END KILLHUMAN.PHP -->


<!-- TEST TABLE #2 -->

<center>
 <?php
  try {
    $query = "SELECT username, status, KillCount, StarveDate FROM members WHERE status='zombie'";

  print "
    <form action='sandbox.php' method='post'>
    Input Victim Usercode: <input type='text' name='hex' required><BR>
    <div id='playerlist' class='playerlist' data-max-answers='2' style='width:100%;height:300px;overflow:auto;''>
    <table id='table1'>
    <tr class='subheader orange'>
    <th>Select</th>
    <th onclick='sortTable(1)'>Username</th>
    <th onclick='sortTable(2)'>Status</th>
    <th onclick='sortTable(3)'>Kill Count</th>
    <th onclick='sortTable(4)'>Starve Date</th>
    </tr>
    
  ";

  $data = $db->query($query);
  $data->setFetchMode(PDO::FETCH_ASSOC);
  foreach($data as $row){
   print " 
      <tr>
      <td>

      
      <input type='checkbox' name='check_list[]' value='$row[username]'>
   ";
   foreach ($row as $name=>$value){
   print " <td>$value</td>";
   } // end field loop
   print " </tr>";
  } // end record loop
  print "</table>";
  } catch(PDOException $e) {
   echo 'ERROR: ' . $e->getMessage();
  } // end try
 ?>

 </center>
 </div>

 <center>
 <input type="submit" name="submit" value="Submit"></center>
</form>

<?php 

if (isset($_POST['check_list'])) 
{
    //print_r($_POST['check_list']); 
    $zombieFeedto = $_POST['check_list'];
    //print_r($zombieFeedto);



$hex = $_POST['hex'];
$victim = findVictim($cxn, $hex);


// UPDATES STARVE DATES
// Takes victim's and zombie's email address
function updateStarve($cxn, $victim, $zombieFeedto, $zombieFeeder)
{
    date_default_timezone_set('America/Denver');
    
    $currTime = date('Y-m-d H:i:s');
    
    // GET NEW STARVE TIME
    $twoHrFut_Str = strtotime("$currTime +2 days");
    $twoHrFut_Unix = date('Y-m-d H:i', $twoHrFut_Str);
    $targetTime = date('Y-m-d H:i:s', strtotime($twoHrFut_Unix));
    
    // STARVE TIMER FOR ZOMBIE
    $query_updateZombieStarve = "UPDATE members SET StarveDate='$targetTime' WHERE username IN('" . implode("','", array_map('trim', $zombieFeedto)) ."')";
    $result_updateZombieStarve = mysqli_query($cxn,$query_updateZombieStarve) or die ("could not execute query_updateZombieStarve");
    
    $query_killRow = "SELECT * FROM members WHERE username='$zombieFeeder'";
    $result_killRow = mysqli_query($cxn,$query_killRow) or die ("could not execute query_killRow");
    $row_killRow = mysqli_fetch_array($result_killRow);
    $currKill = $row_killRow['KillCount'];
    
    $newKill = $currKill + 1;
    $query_updateKill = "UPDATE members SET KillCount='$newKill' WHERE username='$zombieFeeder'";
    $result_updateKill = mysqli_query($cxn,$query_updateKill) or die ("could not execute query_updateKill");
    
    // STARVE TIMER FOR HUMAN-NOW-ZOMBIE
    $query_newZombieStarve = "UPDATE members SET StarveDate='$targetTime' WHERE username = '$victim'";
    $result_newZombieStarve = mysqli_query($cxn,$query_newZombieStarve) or die ("could not execute query_newZombieStarve");
    
    return TRUE;
}


if(strcmp($victim, "none") == 0)
{
    echo "Not a valid code <br>";
}
else
{
    $killReg = regKill($cxn, $victim);
    if($killReg)
    {
        echo "Kill Registered <br>";
        $starveUpdate = updateStarve($cxn, $victim, $zombieFeedto, $zombieFeeder);
        if($starveUpdate)
        {
            echo "Your starve date has been updated.";
        }
    }
}

}

?>

<!-- END TEST TABLE #2 -->





























<!-- TEST TABLE -->
<!--
<div id='playerlist' class='playerlist' data-max-answers='2' style="height:300px;overflow:auto;">
<center>
 <?php /*
  try {
    $query = "SELECT username, status, KillCount, StarveDate FROM members";

  print "
    <form action='#' method='post'>
    <table id='table1'>
    <tr class='subheader orange'>
    <th>Select</th>
    <th onclick='sortTable(1)'>Username</th>
    <th onclick='sortTable(2)'>Status</th>
    <th onclick='sortTable(3)'>Kill Count</th>
    <th onclick='sortTable(4)'>Starve Date</th>
    </tr>
    
  ";

  //second query gets the data
  $data = $db->query($query);
  $data->setFetchMode(PDO::FETCH_ASSOC);
  foreach($data as $row){
   print " 
      <tr>
      <td>

      
      <input type='checkbox' name='check_list[]' value='$row[username]'>
   ";
   foreach ($row as $name=>$value){
   print " <td>$value</td>";
   } // end field loop
   print " </tr>";
  } // end record loop
  print "</table>";
  } catch(PDOException $e) {
   echo 'ERROR: ' . $e->getMessage();
  } // end try
 ?>

 </center>
 </div>

 <center><input type="submit" name="submit" value="Submit"/></center>
</form>

<?php 
if (isset($_POST['check_list'])) 
{
    print_r($_POST['check_list']); 
} */
?>

-->
<!-- END TEST TABLE -->



<!-- FETCH TABLE -->
<!--
<?php /* 

try {
        $total  = $db->query("SELECT COUNT(memberID) as rows FROM members")
                  ->fetch(PDO::FETCH_OBJ);

        $perpage = 3;
        $posts  = $total->rows;
        $pages  = ceil($posts / $perpage);

        # default
        $get_pages = isset($_GET['page']) ? $_GET['page'] : 1;

        $data = array(

            'options' => array(
                'default'   => 1,
                'min_range' => 1,
                'max_range' => $pages
                )
        );

        $number = trim($get_pages);
        $number = filter_var($number, FILTER_VALIDATE_INT, $data);
        $range  = $perpage * ($number - 1);

        $prev = $number - 1;
        $next = $number + 1;

        $stmt = $db->prepare("SELECT username, status, KillCount, StarveDate FROM members ORDER BY StarveDate, username LIMIT :limit, :perpage");
        $stmt->bindParam(':perpage', $perpage, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $range, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetchAll();

    } catch(PDOException $e) {
        $error = $e->getMessage();
    }

    $db = null;
?>


<div class="darkslide center">

            <?php 

                if($result && count($result) > 0)
                {
                    echo "
                    <div id='posts' class='posts center question sortable' data-max-answers='2'>
                        <h3>Gamer List</h3><center>
                        <table id='table1'>
                            <thead>
                                <tr>
                                    <th>Select</th>
                                    <th onclick='sortTable(1)''>Name</th>
                                    <th onclick='sortTable(2)''>Status</th>
                                    <th onclick='sortTable(3)''>Kill Count</th>
                                    <th onclick='sortTable(4)''>Starve Date</th>
                                </tr>
                            <tbody>
                    ";
                        foreach($result as $key => $row)
                        {
                            echo "
                                
                                <tr>
                                    <td><input type='checkbox' name='answer1[]' value='$row[username]'>
                                    <td>$row[username]
                                    <td>$row[status]
                                    <td>$row[KillCount]
                                    <td>$row[StarveDate]

                            ";
                        }


                    echo '
                        </table>
                    </div>
                    ';
                }

            ?>
        

        <div class="navigation">
        
        <?php
            
            if($result && count($result) > 0)
            {
                echo "<h3>Total pages ($pages)</h3>";

                # first page
                if($number <= 1)
                    echo "<span>&laquo; prev</span> | <a href=\"?page=$next\">next &raquo;</a>";
                
                # last page
                elseif($number >= $pages)
                    echo "<a href=\"?page=$prev\">&laquo; prev</a> | <span>next &raquo;</span>";
                
                # in range
                else
                    echo "<a href=\"?page=$prev\">&laquo; prev</a> | <a href=\"?page=$next\">next &raquo;</a>";
            }

            else
            {
                echo "<p>No results found.</p>";
            } 
           
        */ ?>
        </div>
-->
<!-- END FETCH TABLE -->





<!--
<div class="darkslide">

<center>

<?php /**
echo "<table style='border: solid 1px #D1D1D1;'>";
echo "<tr><th>Username</th><th>Status</th><th>Kill Count</th><th>Starve Date</th></tr>";

class TableRows extends RecursiveIteratorIterator { 
    function __construct($it) { 
        parent::__construct($it, self::LEAVES_ONLY); 
    }

    function current() {
        return "<td style='width:150px;border:1px solid #D1D1D1;'>" . parent::current(). "</td>";
    }

    function beginChildren() { 
        echo "<tr>"; 
    } 

    function endChildren() { 
        echo "</tr>" . "\n";
    } 
} 

try {
    $stmt = $db->prepare("SELECT username, status, killCount, starveDate FROM members"); 
    $stmt->execute();

    // set the resulting array to associative
    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC); 
    foreach(new TableRows(new RecursiveArrayIterator($stmt->fetchAll())) as $k=>$v) { 
        echo $v;
    }
}
catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
$conn = null;
echo "</table>"; **/
?>


</center>

</div> -->





<!-- FINAL ORIENTATION SELECTION UPDATE

    <?php /**
    if(isset($_POST['orientSubmit'])){
        $selected_val = $_POST['orientTime'];  // Storing Selected Value In Variable
    } else {
        $selected_val = "2:00pm";
    } 
 
?>

    <form action="#" method="post">
    <select name="orientTime">
    <option value="2:00pm" <?php if ($selected_val == '2:00pm' ) echo 'selected' ; ?> >Session 1 - 2:00pm</option>
    <option value="2:20pm" <?php if ($selected_val == '2:20pm' ) echo 'selected' ; ?> >Session 2 - 2:20pm</option>
    <option value="2:40pm" <?php if ($selected_val == '2:40pm' ) echo 'selected' ; ?>>Session 3 - 2:40pm</option>
    </select><br>
    <input type="submit" name="orientSubmit" class="btn btn-primary btn-block btn-lg button-primary" value="Sign up" />
    </form>

    <?php
    if(isset($_POST['orientSubmit'])){
        $selected_val = $_POST['orientTime'];  // Storing Selected Value In Variable

        try { 

            $username = $_SESSION['username'];

            $sql = "UPDATE members SET orient='$selected_val' WHERE username='$username'";
            // Prepare statement
            $stmt = $db->prepare($sql);
            // execute the query
            $stmt->execute();
            // echo success
            echo "<span class='bg-success'>You have successfully registered for the <span class='subheader'>" . $selected_val . " </span>session.</span><br><br>";
            // echo a message to say the UPDATE succeeded
            echo $stmt->rowCount() . " records UPDATED successfully <br>";

            }

        catch(PDOException $e)
            {
            echo $sql . "<br>" . $e->getMessage();
            }

        } **/
    ?>

-->







<!-- UPDATE WITH SELECT DROPDOWN OPTION

<?php /**
    if(isset($_POST['formSubmit'])) 
    {
        $aCountries = $_POST['orient'];
        
        if(!isset($aCountries)) 
        {
            echo("<p>You didn't select any countries!</p>\n");
        } 
        else 
        {
            

            try { 

            $username = $_SESSION['username'];

            $sql = "UPDATE members SET orient='$aCountries' WHERE username='$username'";
            // Prepare statement
            $stmt = $db->prepare($sql);
            // execute the query
            $stmt->execute();
            // echo a message to say the UPDATE succeeded
            echo $stmt->rowCount() . " records UPDATED successfully";
            }
            

            catch(PDOException $e)
            {
            echo $sql . "<br>" . $e->getMessage();
            }


        }
    }
**/
?>

<form method="post">
    <label for='orient'>Select the countries that you have visited:</label><br>

    <select name="orient">
        <option value="US">United States</option>
        <option value="UK">United Kingdom</option>
        <option value="France">France</option>
        <option value="Mexico">Mexico</option>
        <option value="Russia">Russia</option>
        <option value="Japan">Japan</option>
    </select>

    <br>
    <input type="submit" name="formSubmit" value="Submit" >
</form>


-->







<!-- UPDATE WITH BUTTON

    <?php /**


        try { 

        $username = $_SESSION['username'];

            if (isset($_POST['session1'])){

        $sql = "UPDATE members SET orient='session 1' WHERE username='$username'";
        // Prepare statement
        $stmt = $db->prepare($sql);
        // execute the query
        $stmt->execute();
        // echo a message to say the UPDATE succeeded
        echo $stmt->rowCount() . " records UPDATED successfully";
        }
    }

    catch(PDOException $e)
        {
        echo $sql . "<br>" . $e->getMessage();
        } **/

    ?>

<form method="post">

    <input type="submit" name="session1" value="session 1">

    <input type="submit" name="session2" value="session 2">

    <input type="submit" name="session3" value="session 3">

</form>

--> 





<!--  UPDATE WITHOUT BUTTON

    try { 

        $sql = "UPDATE members SET orient='session 3' WHERE memberID=6";
        // Prepare statement
        $stmt = $db->prepare($sql);
        // execute the query
        $stmt->execute();
        // echo a message to say the UPDATE succeeded
        echo $stmt->rowCount() . " records UPDATED successfully";
        }

    catch(PDOException $e)
        {
        echo $sql . "<br>" . $e->getMessage();
        }

        -->



<!-- SAMPLE BUTTON

    <input type="submit" name="Submit1" value="Submit" class="btn btn-primary btn-block btn-lg button-primary" tabindex="7"> 

    -->

  <script src="js/sort.js"></script>

</body>

</html>