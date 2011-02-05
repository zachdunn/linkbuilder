<?php
//Take a text file of video ID codes and output them as links

class Link_Builder 
{

	public $website; /* Slug of site preset to use*/
	public $base_url;
	public $file_location;
	public $input_type;
	public $total_count;
	public $links; /* Array of links */
	
	//Special markdown-esque blocks (Optional)
	public $highlight_code = "[highlight]";
	
	function __construct($config)
	{		
		extract($config);
		
		/*
			Get the target site's URL structure
		*/
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
		
		//Set type of file (TXT, JSON)
		if (!empty($input_type)) :
			$this->input_type = $input_type;
		endif;
		
		//Set up custom codes
		if (!empty($highlight)) :
			$this->highlight_code = strtolower($highlight);
		endif;
		
		/*
			Get location of links to load
		*/
		if (!empty($file_location)) :
			$this->file_location = $file_location;
		elseif (!empty($links)):
			//Use the direct input array instead
			$this->links = $links;
			$this->input_type = 'array';
		else:
			//We need a file location
			$this->links = false;
		endif;
		
		//Package the object's $link array based on type of input
		$this->package_links();
	}
	
	/*
		JSON outputs are a work in progress.
	*/
	public function package_links()
	{
		switch(strtolower($this->input_type)) {
	
			case 'text':
				$this->links = $this->prepare_text_file();
				break;
			case 'json':
				//Add JSON support later
				break;
			case 'array':
				$this->links = $this->prepare_array();
				break;
			default:
				//Treat it as a direct input by default
				$this->links = $this->prepare_array();
				break;
				
		}
		
		$this->total_count = count($this->links);
	}
	
	
	public function get_json()
	{
		//Return as a JSON array
		$json_version['base_url'] = $this->base_url;
		$json_version['file_location'] = $this->file_location;
		$json_version['highlight'] = $this->highlight_code;
		$json_version['website'] = $this->website;
		$json_version['total_count'] = $this->total_count;
		$json_version['links'] = $this->links;
		
		return stripcslashes(json_encode($json_version));
	}
	
	/*
		Handle direct input of an array via $config
		This method only supports basic URL output
		Future will allow multi-dimensional assc. arrays
	*/
	
	private function prepare_array()
	{
		$updated_links = array();
		foreach($this->links as $page_code):
			$link_info = array(
				'style' => '',
				'full_url' => $this->base_url . $page_code,
				'page_code' => $page_code,
				'description' => '',
			);
			array_push($updated_links, $link_info);
		endforeach;
		
		return $updated_links;
	}
	
	private function prepare_text_file()
	{
		//Outputs a table of all links & descriptions
		if (file_exists($this->file_location)) :
		
	    	$file = fopen($this->file_location,'r');
			
			$all_links = array();
			
	    	while(!feof($file)) : 
				
				$style = null;
				
				$current_line = fgets($file);
				//Use hyphens to separate ID from description (Will add customization options later)
				$current_line = explode('-', $current_line);
				
				//Specific Elements
				$description = (!empty($current_line[1])) ? trim($current_line[1]) : '';
				$page_code = trim($current_line[0]);
				
				//Compile a complete url
				$full_url = $this->base_url . $page_code;
				
				//Override for special styles
				if($this->is_recommended($description)) :
					$style = 'highlight';
					$description = trim(str_ireplace($this->highlight_code, '', $description));
				endif;
				
				//Make an array of this line
				$link_info = array(
					'style' => $style,
					'full_url' => $full_url,
					'page_code' => $page_code,
					'description' =>$description,
				);
				
				array_push($all_links, $link_info);
				
			endwhile;
			
			fclose($file);
			
			//return array of links
			return $all_links;
		else :
			//No entries found
			return false;
		endif;
	}
	
	public function display_links()
	{
	
		//If no links are found, display an error
		if (!$this->links) :
			echo '<p>No links were found</p>';
			return false;
		endif;
		
		//Outputs a table of all links & descriptions
		echo '<table border=1>';
		echo '<thead>';
		echo '	<th>Link (' . $this->total_count .' total)</th>';
		echo '	<th>Description</th>';
		echo '</thead>';
		
		if($this->total_count >= 1) :
			$even_line = false;

	    	foreach($this->links as $link) : 
				
				//Zebra stripes class
				$link['style'] .= ($even_line) ? '' : ' alt';
				
				//Output the row in table format
				echo sprintf('<tr class="%s"><td><a href="%s" target="_blank">%s</a></td><td>%s</td></tr>', $link['style'], $link['full_url'], $link['page_code'], $link['description']);

				//Toggle even & odd lines
				$even_line = ($even_line) ? false : true;
			endforeach;

		else :
			echo('<tr><td colspan=2>No links found</td></tr>');
		endif;
		
		echo '</table>';    

	}
	
	/*
		Support some sites by default based on provided slug
	*/
	
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
		
		//Couldn't find a matching site. We'll need a URL provided
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