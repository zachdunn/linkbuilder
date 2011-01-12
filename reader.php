<?php
require_once 'Link_Builder.php';

$config = array(
	'website'		=>	'youtube',
	'file_location'	=>	'examples/youtube.txt',
);

$youtube = new Link_Builder($config); ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8"/>

	<title>Link Viewer</title>
	<style>
		body{font-family:"Lucida Grande", Helvetica, Arial, sans-serif;}

		/* Helper Classes */
		.alt{background:#EFEFEF;}
		.recommended{background:#FBEC5D; font-weight:bold;}
			.recommended a{color:#A90000;}
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
	
	<table border=1>
		<thead>
			<th>Link (<?php echo $youtube->total_count; ?> total)</th>
			<th>Description</th>
		</thead>
		<?php $youtube->display_links(); ?>
	</table>
	
</body>
</html>