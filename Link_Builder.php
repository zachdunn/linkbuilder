<?php
//Take a text file of video ID codes and output them as links
ini_set('auto_detect_line_endings', true);

class Link_Builder 
{

	public $website; /* Name (slug) of site */
	public $base_url;
	public $file_location;
	public $total_count;
	
	public $links_json;
	
	//Special markdown-esque blocks
	private $highlight_code = "[highlight]";
	
	function __construct($config)
	{		
		extract($config);
		
		//If the website slug has been included
		if (!empty($website)) :
			//See if we have it on file, and populate if so
			if (!$this->autofill_site($website) && !empty($base_url)) :
				//If not, use the supplied base URL
				$this->base_url = $base_url;
			endif;
		else:
			//The website was empty
			$this->base_url = $base_url;
		endif;
		
		//Set up custom codes
		if (!empty($highlight)) :
			$this->highlight_code = strtolower($highlight);
		endif;
		
		if (!empty($file_location)) :
			$this->file_location = $file_location;
			
			//Count number of lines in file (a.k.a. links)
			$this->total_count = count(file($file_location));
		else :
			//We need a file location
			return false;
		endif;
		
		//Put file contents to JSON array
		$this->package_links();
		
	}
	
	/*
		JSON outputs are a work in progress.
	*/
	public function package_links()
	{
		//Update the JSON array to match
		$json_version['base_url'] = $this->base_url;
		$json_version['file_location'] = $this->file_location;
		$json_version['highlight'] = $this->highlight_code;
		$json_version['website'] = $this->website;
		$json_version['total_count'] = $this->total_count;
		
		//Link support isn't active yet
		$json_version['links'] = array();
		
		//Encode to JSON
		$this->links_json = stripcslashes(json_encode($json_version));
	}
	
	public function get_json($obj_type = null)
	{
		if ($obj_type == true) :
			//Return as an object
			return json_decode($this->links_json);
		else :
			//Return as a JSON array
			return $this->links_json;
		endif;
	}
	
	public function display_links()
	{
		//Outputs a table of all links & descriptions
		if (file_exists($this->file_location)) :
		
	    	$file = fopen($this->file_location,'r');
	
			$even_line = false;

	    	while(!feof($file)) : 
	
				$current_line = fgets($file);
				$current_line = explode('-', $current_line);
				
				//Specific Elements
				$description = (!empty($current_line[1])) ? trim($current_line[1]) : '';
				$page_code = trim($current_line[0]);
				
				//Compile a complete url
				$full_url = $this->base_url . $page_code;
				
				//Zebra stripes class
				$style = ($even_line) ? '' : 'alt';
				
				//Override for special styles
				if($this->is_recommended($description)) :
					$style = 'highlight';
					$description = trim(str_ireplace($this->highlight_code, '', $description));
				endif;
				
				//Output the row in table format
				echo sprintf('<tr class="%s"><td><a href="%s" target="_blank">%s</a></td><td>%s</td></tr>', $style, $full_url, $page_code, $description);

				//Toggle even & odd lines
				$even_line = ($even_line) ? false : true;
			endwhile;
			
			fclose($file);

		else :
			echo('<tr><td colspan=2>No links found</td></tr>');
		endif;       

	}
	
	private function autofill_site($site_id)
	{
		//An array of default sites (Add more to call by slug)
		$supported_sites = array(
			'vimeo'		=>	"http://vimeo.com/",
			'youtube'	=>	"http://www.youtube.com/watch?v=",
		);
		
		if ( !empty($supported_sites[$site_id]) ) :
			$this->base_url = $supported_sites[$site_id];
			$this->website = $site_id;
			return true;
		endif;
		
		//Couldn't find a matching site. You'll need a URL
		return false;
	}
	
	/* 
		This is for extra tagging, similar to markdown 
	*/
	public function is_recommended($description)
	{
		$description = strtolower($description);
		if (strlen (strstr ($description, $this->highlight_code)) > 0) {
			//Tagged for recommended
			return true;
		}
		return false;
	}
	
	
	
}