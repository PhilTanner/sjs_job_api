<?php
	class SJS_data_API extends SJS_API
	{
		public $regionslocations = '';
		
		public function SJS_data_API( $version )
		{
			$this->SJS_API( $version );
		}
		
		public function get_regions( )
		{
			$regions = array();
			$regions[0] = new stdClass();
			$regions[0]->id = 838;
			$regions[0]->name = 'All Regions';
			$regions[0]->locations = array();
			$regions[0]->coordinates = array(array(0,0,0,0));

			$regions[1] = new stdClass();
			$regions[1]->id = 324;
			$regions[1]->name = 'Auckland Region';
			$regions[1]->locations = array();
			$regions[1]->coordinates = array(array(-36.418873,174.036202,-37.271670,175.436958));

			$regions[2] = new stdClass();
			$regions[2]->id = 338;
			$regions[2]->name = 'Bay of Plenty Region';
			$regions[2]->locations = array();
			$regions[2]->coordinates = array(array(0,0,0,0));

			$regions[3] = new stdClass();
			$regions[3]->id = 45;
			$regions[3]->name = 'Canterbury Region';
			$regions[3]->locations = array();
			$regions[3]->coordinates = array(array(0,0,0,0));

			$regions[4] = new stdClass();
			$regions[4]->id = 398;
			$regions[4]->name = 'East Cape Region';
			$regions[4]->locations = array();
			$regions[4]->coordinates = array(array(0,0,0,0));

			$regions[5] = new stdClass();
			$regions[5]->id = 346;
			$regions[5]->name = 'Hawke\'s Bay Region';
			$regions[5]->locations = array();
			$regions[5]->coordinates = array(array(0,0,0,0));

			$regions[6] = new stdClass();
			$regions[6]->id = 356;
			$regions[6]->name = 'Manawatu-Wanganui Region';
			$regions[6]->locations = array();
			$regions[6]->coordinates = array(array(0,0,0,0));

			$regions[7] = new stdClass();
			$regions[7]->id = 409;
			$regions[7]->name = 'Marlborough Region';
			$regions[7]->locations = array();
			$regions[7]->coordinates = array(array(0,0,0,0));

			$regions[8] = new stdClass();
			$regions[8]->id = 360;
			$regions[8]->name = 'Nelson Region';
			$regions[8]->locations = array();
			$regions[8]->coordinates = array(array(0,0,0,0));

			$regions[9] = new stdClass();
			$regions[9]->id = 334;
			$regions[9]->name = 'Northland Region';
			$regions[9]->locations = array();
			$regions[9]->coordinates = array(array(0,0,0,0));

			$regions[10] = new stdClass();
			$regions[10]->id = 328;
			$regions[10]->name = 'Otago Region';
			$regions[10]->locations = array();
			$regions[10]->coordinates = array(array(0,0,0,0));

			$regions[11] = new stdClass();
			$regions[11]->id = 412;
			$regions[11]->name = 'Other';
			$regions[11]->locations = array();
			$regions[11]->coordinates = array(array(0,0,0,0));

			$regions[12] = new stdClass();
			$regions[12]->id = 350;
			$regions[12]->name = 'Southland Region';
			$regions[12]->locations = array();
			$regions[12]->coordinates = array(array(0,0,0,0));

			$regions[13] = new stdClass();
			$regions[13]->id = 330;
			$regions[13]->name = 'Taranaki Region';
			$regions[13]->locations = array();
			$regions[13]->coordinates = array(array(0,0,0,0));

			$regions[14] = new stdClass();
			$regions[14]->id = 520;
			$regions[14]->name = 'Tasman Region';
			$regions[14]->locations = array();
			$regions[14]->coordinates = array(array(0,0,0,0));

			$regions[15] = new stdClass();
			$regions[15]->id = 326;
			$regions[15]->name = 'Waikato Region';
			$regions[15]->locations = array();
			$regions[15]->coordinates = array(array(0,0,0,0));

			$regions[16] = new stdClass();
			$regions[16]->id = 340;
			$regions[16]->name = 'Wellington Region';
			$regions[16]->locations = array();
			$regions[16]->coordinates = array(array(0,0,0,0));

			$regions[17] = new stdClass();
			$regions[17]->id = 554;
			$regions[17]->name = 'West Coast Region';
			$regions[17]->locations = array();
			$regions[17]->coordinates = array(array(0,0,0,0));
			
			return $regions;
		}
		
		public function get_locations( $regionid=null )
		{ 
			return 0; 
		}
		
		public function get_job_type()
		{
			$types = array();
			
			$types[0] = new stdClass();
			$types[0]->id = 'apprenticeship';
			$types[0]->name = 'Apprenticeship';
			
			$types[1] = new stdClass();
			$types[1]->id = 'casual';
			$types[1]->name = 'Casual';
			
			$types[2] = new stdClass();
			$types[2]->id = 'contract';
			$types[2]->name = 'Fixed Price Contract';
			
			$types[3] = new stdClass();
			$types[3]->id = 'ft';
			$types[3]->name = 'Fixed-term';
			
			$types[4] = new stdClass();
			$types[4]->id = 'oneoff';
			$types[4]->name = 'One-off';
			
			$types[5] = new stdClass();
			$types[5]->id = 'pft';
			$types[5]->name = 'Permanent Full-time';
			
			$types[6] = new stdClass();
			$types[6]->id = 'ppt';
			$types[6]->name = 'Permanent Part-time';
			
			$types[7] = new stdClass();
			$types[7]->id = 'summer';
			$types[7]->name = 'Summer';
			
			$types[8] = new stdClass();
			$types[8]->id = 'voluntary';
			$types[8]->name = 'Voluntary';
			
			return $types;
		}
		
		public function get_work_category()
		{
			$categories = array();
			$categories[0] = new stdClass();
			$categories[0]->id = 300;
			$categories[0]->name = 'Accounting';
			$categories[1] = new stdClass();
			$categories[1]->id = 292;
			$categories[1]->name = 'Administration &amp; Office Support';
			$categories[2] = new stdClass();
			$categories[2]->id = 280;
			$categories[2]->name = 'Advertising, Arts &amp; Media';
			$categories[3] = new stdClass();
			$categories[3]->id = 267;
			$categories[3]->name = 'Banking, Finance &amp; Insurance';
			$categories[4] = new stdClass();
			$categories[4]->id = 261;
			$categories[4]->name = 'Call Centre &amp; Customer Service';
			$categories[5] = new stdClass();
			$categories[5]->id = 249;
			$categories[5]->name = 'Construction';
			$categories[6] = new stdClass();
			$categories[6]->id = 237;
			$categories[6]->name = 'Design &amp; Architecture';
			$categories[7] = new stdClass();
			$categories[7]->id = 224;
			$categories[7]->name = 'Education, Training &amp; Childcare';
			$categories[8] = new stdClass();
			$categories[8]->id = 210;
			$categories[8]->name = 'Engineering';
			$categories[9] = new stdClass();
			$categories[9]->id = 199;
			$categories[9]->name = 'Farmwork, Agriculture Fishing &amp; Forestry';
			$categories[10] = new stdClass();
			$categories[10]->id = 194;
			$categories[10]->name = 'Government &amp; Council';
			$categories[11] = new stdClass();
			$categories[11]->id = 161;
			$categories[11]->name = 'HR &amp; Recruitment';
			$categories[12] = new stdClass();
			$categories[12]->id = 166;
			$categories[12]->name = 'Healthcare';
			$categories[13] = new stdClass();
			$categories[13]->id = 180;
			$categories[13]->name = 'Hospitality &amp; Tourism';
			$categories[14] = new stdClass();
			$categories[14]->id = 750;
			$categories[14]->name = 'Household';
			$categories[15] = new stdClass();
			$categories[15]->id = 141;
			$categories[15]->name = 'Information &amp; Communication Technology';
			$categories[16] = new stdClass();
			$categories[16]->id = 136;
			$categories[16]->name = 'Law';
			$categories[17] = new stdClass();
			$categories[17]->id = 118;
			$categories[17]->name = 'Manufacturing, Transport &amp; Logistics';
			$categories[18] = new stdClass();
			$categories[18]->id = 107;
			$categories[18]->name = 'Marketing &amp; Communications';
			$categories[19] = new stdClass();
			$categories[19]->id = 313;
			$categories[19]->name = 'Not specified';
			$categories[20] = new stdClass();
			$categories[20]->id = 79;
			$categories[20]->name = 'Retail';
			$categories[21] = new stdClass();
			$categories[21]->id = 97;
			$categories[21]->name = 'Sales';
			$categories[22] = new stdClass();
			$categories[22]->id = 91;
			$categories[22]->name = 'Science &amp; Technology';
			$categories[23] = new stdClass();
			$categories[23]->id = 86;
			$categories[23]->name = 'Sport &amp; Recreation';
			$categories[24] = new stdClass();
			$categories[24]->id = 64;
			$categories[24]->name = 'Trades Assistance';
			
			return $categories;
		}

		public function show_documentation()
		{
			echo '<html><body><h1>SJS Job API Documentation</h1><h2>Version '.$_GET['version'].'</h2>';
			echo '<h3>Changelog:</h3><ul>';
			switch( $this->version )
			{
				case "0.1":
					echo '<li>0.1 2014/05/28 - Initial release</li>';
					echo '</ul><h3>Inputs:</h3>';
					echo '<table border="1"><thead><tr><th>Name</th><th>Data type</th><th>Valid value(s)</th><th>Required</th><th>Description</th></tr></thead><tbody>';
					echo '<tr><td>version</td><td>string</td><td>'.SJS_API_VERSIONS.'</td><td>Yes</td><td>The version number of the API you are calling.</td></tr>';
					echo '<tr><td>format</td><td>string</td><td>'.SJS_API_FORMATS.'</td><td>Yes</td><td>What format you want the data returned in.</td></tr>';
					echo '<tr><td>callback</td><td>string</td><td></td><td>Yes if format=jsonp, otherwise No</td><td>The name of the callback function wrapper for JSONP queries.</td></tr>';
					echo '<tr><td>reference</td><td>int</td><td></td><td>Yes</td><td>A valid SJS job reference.</td></tr>';
					echo '</tbody></table>';
					break;
				default:
					break;
			}
			echo '</body></html>';
		}
	}
?>
