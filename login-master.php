<?php
//include config
require_once('includes/config.php');

// check if already logged in move to home page
if( $user->is_logged_in() ){ header('Location: profile.php'); } 

// process login form if submitted
if(isset($_POST['submit'])){

	$username = $_POST['username'];
	$password = $_POST['password'];
	
	if($user->login($username,$password)){ 
		$_SESSION['username'] = $username;
		header('Location: profile.php');
		exit;
	
	} else {
		$error[] = 'Wrong username or password or your account has not been activated.';
	}

}// end if submit

// define page title
$title = 'HVZ CU BOULDER';

// include header template
require('layout/header.php'); 
?>

<!-- Begin Document
–––––––––––––––––––––––––––––––––––––––––––––––––– -->

<nav>
<center>
<a href="#playerinfo" class="cta">What is HVZ? Click to learn more.</a>
</center>
</nav>


<div class="lightslide">

<div class="container">

	<div class="row">

	<!-- HEADLINE -->
    <div class="five columns">
      <h1 class="section-heading">Humans
      <span class="white">versus</span> Zombies</h1>
      <h2 class="grey subheader">University of Colorado <strong class="deeporange">Boulder</strong></h2>
      <img src="images/skull.png" class="u-max-full-width">
    </div> <!-- end headline -->

	 
	 <div class="six columns lightslide-box">
      <h4 class="white">Please login.</h4>
      <span class="white">Weeklong Game</span> (March 20th - March 24th).
      <p>Registered users, please sign in to play.</p>
      <hr>
	  <!--<p>Not a member? <a href='./'>Sign-up now.</a></p>-->

			<form role="form" method="post" action="" autocomplete="on">

				<?php
				//check for any errors
				if(isset($error)){
					foreach($error as $error){
						echo '<p class="bg-danger">'.$error.'</p>';
					}
				}

				if(isset($_GET['action'])){

					//check the action
					switch ($_GET['action']) {
						case 'active':
							echo "<p class='bg-success'>Your account has been activated. Please login.</p>";
							break;
						case 'reset':
							echo "<p class='bg-success'>Please check your inbox for a reset link.</p>";
							break;
						case 'resetAccount':
							echo "<p class='bg-success'>Password changed, you may now login.</p>";
							break;
					}

				}

				
				?>

				<div class="form-group">
					<a href='retrieveUsername.php'>Forgot your Username?</a><br>
					<input type="text" name="username" id="username" class="form-control input-lg" placeholder="Username" value="<?php if(isset($error)){ echo $_POST['username']; } ?>" tabindex="1">
				</div>

				<div class="form-group">
				<a href='reset.php'>Forgot your Password?</a> <br>
					<input type="password" name="password" id="password" class="form-control input-lg" placeholder="Password" tabindex="3">
				</div>
				
				<div class="row">
					<div class="col-xs-6 col-md-6"><input type="submit" name="submit" value="Login" class="btn btn-primary btn-block btn-lg button-primary" tabindex="5"></div>
				</div>

				 <p>
					 <!-- <a href='resendActivate.php'>Click to resend activation link.</a> -->
					 <a href='deleteAccount.php'>Click to Unregister / Delete Account.</a>
				 </p>

			</form>

			<hr>

			<p>Registration for the Spring 2017 Weeklong Game has closed. Missed it? <a href="subscribe.php">Subscribe for future game updates.</a><br></p>
		

		</div>

	</div>

</div>

</div>

<!-- GAME TIME COUNTDOWN SECTION
___________________________________________-->

<?php require('countdown.php'); ?>

<!-- END GAME TIME COUNTDOWN SECTION -->


<section class="darkslide" id="logkill"><center>
<div class="container">
<div class="row">
<div class="twelve columns">

<div class="section-heading">Game Stats</div>

<hr>

<?php

$countusers = $db->query("SELECT count(1) FROM members")->fetchColumn();
$sth = $db->prepare("SELECT status FROM members");
$sth->execute();

/* Fetch all of the values of the first column */
$result = $sth->fetchAll(PDO::FETCH_COLUMN, 0);
//var_dump($result);
//print_r($result);
//print_r(array_count_values($result));
$TotalHuman = array_count_values($result)['human'];
$TotalZombie = array_count_values($result)['zombie'];
$TotalDeceased = array_count_values($result)['deceased'];

?>


<table class="gamestats">
  <thead>
  <tr class='subheader orange'>
    <th>Total Humans</th>
    <th>Total Zombies</th>
    <th>Total Deceased</th>
  </tr>
</thead>

<tbody>
<tr>
<td class="section-heading"><?php echo $TotalHuman ?></td>
<td class="section-heading"><?php echo $TotalZombie ?></td>
<td class="section-heading"><?php echo $TotalDeceased ?></td>
</tr>
</tbody>

</table>

<hr>

<div class="subheadline">Most Recent Activity</div>

<hr>

 <?php
  try {
    $query = "SELECT username, status, KillCount, StarveDate FROM members WHERE status = 'human' OR status = 'zombie' OR status = 'deceased' ORDER BY StarveDate DESC LIMIT 10";

  print "
    
    <table id='table1'>
    <tr class='subheader orange'>
    <th onclick='sortTable(0)'>Username</th>
    <th onclick='sortTable(1)'>Status</th>
    <th onclick='sortTable(2)'>Kill Count</th>
    <th onclick='sortTable(3)' class='starve'>Starve Date</th>
    </tr>
    
  ";

  $data = $db->query($query);
  $data->setFetchMode(PDO::FETCH_ASSOC);
  foreach($data as $row){
   print " 
      <tr>
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
 </div> <!-- end playerlist list -->
 </div>
 </div>
 </center>
 </section>

 <?php 
require('playerinfo.php'); 
?>


<script src="js/sort.js"></script>


<!-- End Document
–––––––––––––––––––––––––––––––––––––––––––––––––– -->

<?php 
require('layout/footer.php'); 
?>
