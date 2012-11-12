<?php
	if(isset($_POST["user"])){ //a user has been given 
		mysql_connect('localhost','root','test123') or die ("database error");
		mysql_select_db("TT");
		$sql="INSERT INTO USERS (user) VALUES('".$_POST["user"]."')"; 
		mysql_query($sql);	//insert the user in the DB
		echo '
			<style type="text/css">
				a:link {color:#0000FF;}    /* unvisited link */
				a:visited {color:#0000FF;} /* visited link */
				a:hover {color:#FF0000;}   /* mouse over link */
			</style>
			<h1 align="center">
				<a href="add.php">Add another user</a>
			<br>
				<a href="main.php">Back</a>
			</h1>
		
		';
	}
	else //form for inputing a new user from github
		echo '
			<h1>Add new Github user</h1>
			<form method= "post" action="add.php">
				<table>
					<tr>
						<td><label class="user">Github User</label></td>
						<td><input type="text" name= "user"></td>
					</tr>
				</table>
				<input type="submit" value= "Submit">
			</form>
	';
?>