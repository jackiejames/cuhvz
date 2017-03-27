<?php require('includes/config.php');

// if not logged in redirect to login page
if(!$user->is_logged_in()){ header('Location: login.php'); } 

// define page title
$title = 'HVZ CU BOULDER';

require('layout/header.php');
?>

<table>
<tr><th>Code</th>
<th>Active</th>
</tr>

<tr>

<?php
$sql = "SELECT * FROM immune";
foreach($db->query($sql) as $row){
	echo "<tr>";
    echo "<td>{$row['immuneCode']}</td>";
    echo "<td>{$row['active']}</td>";
    echo "</tr>";
}
?>

</table>

</body>

</html>