<?php
//Error reporting for debug
ini_set('display_errors', 1);
error_reporting(E_ALL);

//This line is needed for text files to be read by line
ini_set('auto_detect_line_endings', true);

require_once 'Link_Builder.php';

$config = array(
	'website'		=>	'youtube',
	'file_location'	=>	'examples/youtube.txt',
	'input_type'	=>	'text',
);

$youtube = new Link_Builder($config); ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8"/>

	<title>Link Builder</title>
	<style>
		body{font-family:"Lucida Grande", Helvetica, Arial, sans-serif;}

		/* Helper Classes */
		.alt{background:#EFEFEF;}
		.highlight{background:#FBEC5D; font-weight:bold;}
			.highlight a{color:#A90000;}
		table{
			border: 1px solid #CCC;
			color:#333; font-size:12px;
			border-collapse: collapse;
		}
		table th {
			border: 1px dotted #CCC;
			padding: 5px;
			background:#3589d7;
			color:#FFF;
		}
		table td {
			border-width: 1px dotted #CCC;
			padding: 6px;
		}
	</style>
		
</head>

<body>
	<h1>YouTube</h1>
	
	<p>List of popular YouTube videos. The yellow line demonstrates markdown-style highlighting.</p>
	
	<?php $youtube->display_links(); ?>
	
</body>
</html>