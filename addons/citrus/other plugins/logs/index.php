<html>
	<body background = "images/background.gif">
		<head>
			<style type="text/css">
				A:link {color: #333333}
				A:visited {color: #333333}
				A:hover {color: #666666}
			</style>
		</head>
		<?php
			$host = "localhost:3306";
			$user = "";
			$password = "";
			$database = "";
			$table = "Logs";
			$theme = "#65E04D";
			
			// Connect to the database with our given details.
			$connection = mysql_connect($host, $user, $password);
			
			// Check to see if we connected successfully.
			if (!$connection) { return; };
			
			// Select the database that we need to use.
			mysql_select_db($database, $connection);
			
			// We set pages to false here because at the moment we don't need to use them.
			$pages = false;
			
			// Check to see if we have been given a key to use.
			if ($_GET['key']) {
				$_GET['key'] = mysql_escape_string($_GET['key']);
				
				// Get the logs from the database where the key is equal to the one we want.
				$results = mysql_query("SELECT * FROM $table WHERE _Key = '" . $_GET['key'] . "'");
			}else{
				if ($_GET["page"]) { $page = $_GET["page"]; } else { $page = 1; };
				
				// Get the amount of records we have stored in our table.
				$records = mysql_query("SELECT COUNT(_Key) FROM $table");
				$records = mysql_fetch_row($records);
				$records = $records[0];
				$pages = ceil($records / 50);
				
				// Check to see if the page is smaller than 0 or greater than the 50 times the amount of records we have.
				if ($page < 1) { $page = 1; }elseif ($page > $pages) { $page = $pages; };
				
				// Select the logs from the database using our page details.
				$results = mysql_query("SELECT * FROM $table ORDER BY _Key DESC LIMIT " . (($page - 1) * 50) . ", 50");
			};
			
			// A function to draw a row in the table.
			function drawRow($color, $date, $time, $text) {
				echo("<tr align = 'center' bgcolor = '$color'>");
					echo("<td>$date</td>");
					echo("<td>$time</td>");
					echo("<td>$text</td>");
				echo("</tr>");
			};
			
			// A function to draw a simple row in the table.
			function drawSimpleRow($color, $text) {
				echo("<tr align = 'center' bgcolor = '$color'>");
					echo("<td colspan = '7'>$text</td>");
				echo("</tr>");
			};
			
			// Check to if the results exist.
			if (isset($results)) {
				echo("<center><table style = 'border: 1px #333333 solid; font-family: arial; font-size: 12px;' cellspacing = '1' cellpadding = '2' bgcolor = '#FFFFFF' width = 800px>
				<tr>
					<td colspan = '7' style = 'padding: 0;'><img src = 'images/header.png'/></td>
				</tr>
				<tr bgcolor = '$theme'>
					<th>Date</th>
					<th>Time</th>
					<th>Text</th>
				</tr>");
				
				// A list of variables which store the page locations.
				$logs = "<a href = '../logs/'>Logs</a>";
				$bans = "<a href = '../bans/'>Bans</a>";
				$reports = "<a href = '../reports/'>Reports</a>";
				
				// Draw the row that displays the available pages.
				drawSimpleRow("#E5E5E5", "$logs | $bans | $reports");
				
				// We set drawn to false here because we at the moment we haven't drawn any results.
				$drawn = false;
				
				// Check to see if we managed to get the results.
				if ($results) {
					while ($result = mysql_fetch_array($results)) {
						drawRow($result['_Color'], $result['_Date'], $result['_Time'], $result['_Text']);
						
						// Make drawn true because we have drawn at least 1 result.
						$drawn = true;
					};
				};
				
				// Check to see if we drew any results.
				if (!$drawn) {
					drawSimpleRow("#FFFFFF", "Unable to locate any entries in the table!");
				}elseif ($pages) {
					$next = "Next Page";
					$previous = "Previous Page";
					
					// Check to see if we should link to the next page.
					if ($page < $pages) { $next = "<a href = 'index.php?page=" . ($page + 1) . "'>Next Page</a>"; };
					
					// Check to see if we should link to the previous page.
					if ($page > 1) { $previous = "<a href = 'index.php?page=" . ($page - 1) . "'>Previous Page</a>"; };
					
					// Check to see if we even need to draw a next page or previous page link.
					if ($next != "Next Page" or $previous != "Previous Page") {
						drawSimpleRow($theme, "<b>$previous | $next</b>");
					};
				};
				
				// We finish the table here because we are all done.
				echo("</table></center>");
			};
			
			// Close the connection to the database.
			mysql_close($connection);
		?>
	</body>
</html>