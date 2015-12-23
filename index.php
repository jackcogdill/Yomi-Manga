<?php

$img_formats = "jpg,jpeg,gif,png,tiff,bmp"; // Only list image files
$img_formats = explode(",", $img_formats);
$files = glob("./*");
$image_files = array();
foreach ($files as &$file) {
	$file = basename($file);
	$file = urlencode($file);
	$extension = end(explode(".", $file));
	if (in_array($extension, $img_formats)) { // Only include image files
		$image_files[] = $file;
	}
}


// Current - urlencode again because using $_GET decodes it
$current = (isset($_GET["file"])) ? urlencode($_GET["file"]): $image_files[0];

// Previous
$res = array_search($current, $image_files);
if ($res) {
	$previous = $image_files[$res - 1];
	$previous = "?file=$previous";
}

// Next
if ($res + 1 < count($image_files)) {
	$next = $image_files[$res + 1];
	$next = "?file=$next";
}


// Progress
$save = "?file=$current&save";
if (isset($_GET["save"])) {
	$fh = fopen("save.progress", "w");
	fwrite($fh, "$current");
	fclose($fh);
}

if (file_exists("save.progress")) {
	$contents = file_get_contents("save.progress");
	$load = "?file=$contents";
	$need_to_save = $contents !== $current;
}

?>


<html><head>

<style type="text/css">

* {
	margin: 0;
	padding: 0;
}

html, body {
	height: 100%;
}

body {
	background-color: #000000;
	font-family: Sans-serif;
	overflow-y: scroll;
}

#wrap {
	height: 100%;
	width: 100%;
}

#strip-wrap {
	width: 1010px;
	min-height: 100%;
	margin: 0 auto;
	background-color: #333333;
}

.strip-headers {
	background-color: rgba(255, 255, 255, 0.025);
	padding: 10px;
	min-height: 20px;
	text-align: right;
}

.strip-headers a:link {
	text-decoration: none;
	color: #A1CAFC;
	background-color: #2E2E2E;
	padding: 4px 8px;
	vertical-align: middle;
	-webkit-border-radius: 2px;
	-moz-border-radius: 2px;
	-ms-border-radius: 2px;
	-o-border-radius: 2px;
	border-radius: 2px;
}

<?php

if (!isset($need_to_save) || $need_to_save) {
	echo <<<m
#save {
	color: #0099CC;
}

m;
}

?>

.strip-headers a:visited {
	color: #A1CAFC;
}

.strip-headers a:hover {
	text-decoration: underline;
}

#strip {
	text-align: center;
}

#strip img {
	min-width: 800px;
	max-width: 1010px;
	margin: 20px 0;

	-moz-box-shadow: 0 0 24px #222;
	-webkit-box-shadow: 0 0 24px #222;
	box-shadow: 0 0 24px #222;
}

#progress {
	float: left;
}

</style>

<script type="text/javascript">

<?php
if (!isset($need_to_save) || $need_to_save) {
echo <<<m

function prompt(e) {
	var confirmationMessage = "Are you sure you want to leave without saving?";

	(e || window.event).returnValue = confirmationMessage; //Gecko + IE
	return confirmationMessage; //Gecko + Webkit, Safari, Chrome etc.
}

window.addEventListener("beforeunload", prompt);

function leave() {
	window.removeEventListener("beforeunload", prompt);
}

m;
}
?>

document.onkeydown = function (e) {
	e  = e ? e : (event ? event : null);

	switch (e.keyCode) {
		case 37:
<?php

if (isset($previous)) {
	if (!isset($need_to_save) || $need_to_save) {
		echo "leave();";
	}
	echo "window.location.href = \"$previous\";";
}

?>
			break;
		case 39:
<?php

if (isset($next)) {
	if (!isset($need_to_save) || $need_to_save) {
		echo "leave();";
	}
	echo "window.location.href = \"$next\";";
}

?>
			break;
	}
}

</script>

</head><body>
<div id="wrap">



<div id="strip-wrap">

<div class="strip-headers">
<div id="progress">
<?php
if (!isset($need_to_save) || $need_to_save) {
	echo "<a href=\"$save\" id=\"save\" onclick=\"leave()\">Save</a> ";
}
else {
	echo "<a href=\"$save\" id=\"save\">Save</a> ";
}

if (isset($load)) {
	if (!isset($need_to_save) || $need_to_save) {
		echo "<a href=\"$load\" onclick=\"leave()\">Load</a>";
	}
	else {
		echo "<a href=\"$load\">Load</a>";
	}
}
?>
</div>
<?php
if (isset($previous)) {
	if (!isset($need_to_save) || $need_to_save) {
		echo "<a href=\"$previous\" onclick=\"leave()\">Previous</a> ";
	}
	else {
		echo "<a href=\"$previous\">Previous</a> ";
	}
}
if (isset($next)) {
	if (!isset($need_to_save) || $need_to_save) {
		echo "<a href=\"$next\" onclick=\"leave()\">Next</a> ";
	}
	else {
		echo "<a href=\"$next\">Next</a> ";
	}
}
?>
</div>

<div id="strip">
<?php 
echo "<img src=\"$current\">";
?>
</div>

<div class="strip-headers"></div>
</div>



</div>
</body></html>