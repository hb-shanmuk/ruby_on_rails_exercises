<?php session_start();
if(!isset($_SESSION['usr_name'])) {
	header("Location: /blog/login_page.php");
	exit();
}
$usr_name = $_SESSION['usr_name'];
mysql_connect('localhost','priyank','priyank');
mysql_select_db('blog');
if(!isset($_SESSION['name'])) {
	$query = "select name from users where username='$usr_name'" ;
	$result = mysql_query($query);
	while($row = mysql_fetch_array($result)) {
		$name = $row[0];
		$_SESSION['name'] = $name;
	}
} else {
	$name = $_SESSION['name'];
}

/* -- Choosing the category to display -- */

if(!isset($_POST['opt_category']) || $_POST['opt_category'] == 'all') {
	$opt_cat = $_POST['opt_category'];
	$query = "select title,shayari,category,shayari_id,username from shayari order by updated_on desc, posted_on desc";
	$result = mysql_query($query);
} else if(isset($_POST['opt_category']) && $_POST['opt_category'] != 'all') {
	$opt_cat = $_POST['opt_category'];
	$query = "select title,shayari,category,shayari_id,username from shayari where category='$opt_cat' order by updated_on desc, posted_on desc";
	$result = mysql_query($query);
}

$sql = "select image_name from image where username='$usr_name'";
$res = mysql_query($sql);
if(mysql_num_rows($res) == 1) {
	$image_row = mysql_fetch_array($res);
	$image = $image_row['image_name'];
}
?>
<html>
<head>
<title>Welcome to Sher-O-Shayari</title>
<LINK href="/blog//welcome/welcome_pages.css" rel="stylesheet" type="text/css">
<style type='text/css'>
pre {
	font-size :14px;
}
div#rightside textarea {
	resize : none;
}
div#commentleft {
	float:left;
	width:470px;
}
div#commentright {
	float:right;
}
div#commentleft > div > p:first-child {
	font-size: 16px;
}
div#commentleft p {
	font-size:14px;
}
div#commentleft fieldset {
	padding-bottom: 3px;
	margin-top:0;
	margin-bottom:20px;
}
div#commentleft form {
	margin: 0;
}
div#commentleft > div > div > p {
	text-align: right;
	font-size:14px;
	font-style:italic;
}
h2 {
	color:#8d3636;
	margin-top:0;
}
	
</style>
</head>
<body id='main'>
	<div>
		<div id='maindiv'>
			<div id='logout'><a href='/blog/login_page.php' /><img src='/blog/pictures/logout.jpg' /></a></div>
			<h1><span style="color: #700404">S</span><span style="color:#7e1d1d">h</span><span style="color:#8d3636">e</span><span style="color:#9b4f4f">r</span><span style="color:#9b4f4f">-</span><span style="color:#700404">O</span><span style="color:#9b4f4f">-</span><span style="color:#700404">S</span><span style="color:#7e1d1d">h</span><span style="color:#8d3636">a</span><span style="color:#9b4f4f">y</span><span style="color:#a96868">a</span><span style="color:#b88282">r</span><span style="color:#c69b9b">i</span></h1>
			<p id='welcome'> Hi! <?php echo $name ?> </p><br/>
		</div>
		<div id='leftside'>
			<div id='pic'>
				<img src="/blog/upload/<?php echo $image ?>" />
			</div>
			<div id='links'>
				<div class='leftlink'><a href="/blog/welcome/profile/profile.php">Profile</a></div>
				<div class='leftlink' id='shayari'>
					<span id='waste'>Shayaris
					<ul id='to_hide'>
						<li><a href="/blog/welcome/Shayaris/My_Shayari.php">My Shayaris</a></li>
						<li><a href="/blog/welcome/Shayaris/All_Shayaris.php">All Shayaris</a></li>
					</ul></span>
				</div>
				<div class='leftlink'><a href="/blog/welcome/post_shayari/Post_Shayari.php">Post Shayari</a></div>
			</div>
		</div>
		<div id='rightside'>
			<div id='commentleft'>
				<h2>All Shayaris</h2>
				<?php /* -- displaying the shayari and specifies comments -- */
					if(mysql_num_rows($result) == 0) {
						echo "<p>No post in this category</p>";
					} else {
						while ($row = mysql_fetch_array($result)) {
							$shayari_id = $row['shayari_id'];
							echo "<div>";
								echo "<p><b>Title:&nbsp;&nbsp;</b>".$row['title']."</p>";
								echo "<p><b>Category:</b>&nbsp;&nbsp;".$row['category']."</p>";
								echo "<div><b>Shayari:</b><br/><pre>".$row['shayari']."</pre><p>-".$row['username']."</p></div>";
								echo "<fieldset>";
									echo "<legend>Comments</legend>";
									/* -- Displaying the comments to the specified shayari -- */
									echo "<div id='display_comments'>";
										$query2 = "select username, comment from comment where shayari_id = '$shayari_id'";
										$result2 = mysql_query($query2);
										while ($row_comments = mysql_fetch_array($result2)) {
											$show_comment = $row_comments['comment'];
											$show_user = $row_comments['username'];
											echo "<p><b>".$show_user.":</b>&nbsp;&nbsp;".$show_comment."</p>";
										}
									echo "</div>";
									echo "<div>";
										echo "<textarea cols='60' rows='2' name='comment'></textarea><br/>";
										echo "<input type='hidden' name='shayari_id' value='$shayari_id' id='shayari_id'>";
										echo "<input type='submit' name='submit_comment' value='Comment' onclick='postComment(this)' />";
										echo "<input type='button' name='clear' value='Clear' onclick='clear_comment(this)' />";
									echo "</div>";
								echo "</fieldset>";
							echo "</div>";
						}
					}
				?>
			</div>
			<div id='commentright'>
				<form method='post' action='All_Shayaris.php' >
					<select name='opt_category' id='opt_category' onchange='submit()'>
						<option name='val' value='all'>All Categories</option>
						<option name='val' value='love'>Love</option>
						<option name='val' value='hatred'>Hatred</option>
						<option name='val' value='life'>Life</option>
						<option name='val' value='loneliness'>Loneliness</option>
						<option name='val' value='joy'>Joy</option>
						<script type='text/javascript'>
							var val = document.getElementsByName('val');
							for (var i=0;i<val.length;i++) {
								if (val[i].value == '<?php echo $opt_cat; ?>') {
									val[i].selected = 'selected';
									break;
								}
							}
						</script>
					</select>
				</form>
			</div><br/>
		</div><br/>
	</div>
	<script type='text/javascript' src='submit_select.js'>
	</script>
</body>
</html>
