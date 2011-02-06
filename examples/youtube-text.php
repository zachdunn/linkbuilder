<?php
//Error reporting for debug
ini_set('display_errors', 1);
error_reporting(E_ALL);

//This line is needed for text files to be read by line
ini_set('auto_detect_line_endings', true);

require_once '../Link_Builder.php';

$config = array(
	'website'		=>	'youtube',
	'file_location'	=>	'text/youtube.txt',
	'input_type'	=>	'text',
);

$youtube = new Link_Builder($config); ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8"/>

	<title>YouTube Link Builder</title>
	
	<link rel="stylesheet" type="text/css" href="css/style.css"/>
		
</head>

<body>
	<h1>YouTube</h1>
	
	<p>List of popular YouTube videos loaded from a text file. The yellow line demonstrates markdown-style highlighting.</p>
	
	<?php $youtube->display_links(); ?>
	
	<h2>JSON Output</h2>
	
	<div class="raw">
	<?php print_r($youtube->get_json()); ?>
	</div>
	
</body>
</html>