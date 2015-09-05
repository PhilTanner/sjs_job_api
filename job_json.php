<?php
	define( "SJS_API_VERSIONS", "0.1" );
	
	require_once 'class.SJS_API.php';	
	require_once 'class.SJS_job.php';
	require_once 'class.JSONP.php';
	
	class SJS_job_API extends SJS_API
	{
		public $reference         = 0;
		public $job_url	          = 'http://www.sjs.co.nz/job/';
		
		public function SJS_job_API( $version )
		{
			$this->SJS_API( $version );
		}
		
		public function get_job_content( $url )
		{
			$response = $this->get_content( $url );
			
			$pagecontent = $matches = array();
			preg_match('/<div id="main" class="clearfix">(.*)<div id="footer">/mis', $response, $pagecontent );
			
			if( count( $pagecontent ) )
				return $pagecontent[1];
			else 
				throw new SJS_API_No_Results_Error( 'Cannot find details.' );
				
		}
		
		public function get_reference()	{ return $this->reference; }
		public function get_url()	{ return $this->job_url.$this->reference; }

		public function parse_job_html( $html )
		{
			$jobdetails = new SJS_Job( $this->reference );

			// Pull the data displayed in the sidebar			
			preg_match_all('/<div id="sidebar-first" class="columns">(.*)<\/section>/Uis', $html, $sidebar);
			if( count( $sidebar ) )
			{
				preg_match_all('/<th class="views-field (.*)" >(.*)/im', $html, $fields);
				preg_match_all('/<td class="views-field (.*)" >(.*)/im', $html, $fieldvalues);
				if( count( $fields ) && count( $fieldvalues ) )
				{
					for( $i=0; $i<count($fields[1]); $i++ )
					{
						$fieldname = str_replace( 'views-field-', '', $fields[1][$i] );
						switch( $fieldname )
						{
							case 'field-relisted-date':
								$jobdetails->set_date_job_listed( $this->parseDMYdate( $fieldvalues[2][$i]) );
								break;
							case 'created':
								$jobdetails->set_date_job_listed( $this->parseDMYdate( $fieldvalues[2][$i]) );
								break;
							case 'field-location-region-1':
								$jobdetails->set_region( strip_tags($fieldvalues[2][$i]) );
								break;
							case 'field-job-category-text':
								$jobdetails->set_category( trim(strip_tags($fieldvalues[2][$i])) );
								break;
							case 'field-job-sub-category-text':
								$jobdetails->set_sub_category( trim(strip_tags($fieldvalues[2][$i])) );
								break;
							case 'field-job-type':
								$jobdetails->set_job_type( strip_tags($fieldvalues[2][$i]) );
								break;
							case 'field-application-closing-date':
								$jobdetails->set_date_application_closing( $this->parseDMYdate( $fieldvalues[2][$i]) );
								break;
							case 'field-job-start-date':
								$jobdetails->set_date_job_start( $this->parseDMYdate( $fieldvalues[2][$i]) );
								break;
							case 'field-job-end-date':
								$jobdetails->set_date_job_end( $this->parseDMYdate( $fieldvalues[2][$i]) );
								break;
							case 'field-hourly-rate-to':
								$pay_rate = str_replace(array('$',' ',','),'',$fieldvalues[2][$i]);
								if( strpos( $pay_rate, '-' ) !== false )
								{
									$pay_rate = explode('-',$pay_rate);
									$jobdetails->set_pay_rate_from( $pay_rate[0] );
									$jobdetails->set_pay_rate_to( $pay_rate[1] );
								} else {
									$jobdetails->set_pay_rate_from( $pay_rate );
									$jobdetails->set_pay_rate_to( $pay_rate );
								}
								break;
							case 'field-hours-per-week':
								$jobdetails->set_hours_per_week( $fieldvalues[2][$i] );
								break;
							case 'field-job-reference':
								break;
							default:
								throw new SJS_API_Parse_Error('Unknown field "'.$fieldname.'"');
						}
					}
				}
			}

			// Pull the main details			
			preg_match_all('/<h2 property="dc:title" (.*)<\/h2>/Uis', $html, $title);
			if( count( $title ) )
				$jobdetails->set_title( trim(strip_tags($title[0][0])) );

			preg_match_all('/<div class="field field-name-field-job-summary (.*)<div class="field field-name-body /Uis', $html, $summary);
			if( count( $summary ) )
				$jobdetails->set_summary( trim(strip_tags(str_replace('<div class="field-label">Job Summary&nbsp;</div>','',$summary[0][0]))) );

			preg_match_all('/<div class="field field-name-body (.*)<ul class="links inline">/is', $html, $description);
			if( count( $description ) )
			{
				$description = trim(str_replace('<div class="field-label">Job Description&nbsp;</div>','','<div class="field field-name-body '.str_replace('<span class="fieldset-legend">Job Requirements</span>','<h3>Job Requirements</h3>',$description[1][0])));
				$description = strip_tags($description, '<p><a><b><strong><b><em><i><ul><li><ol><br><h3><h2>');
				$jobdetails->set_description( $description );
			}

			if( is_null( $jobdetails->get_category() ) )
				throw new SJS_API_Missing_Field_Error('Failed to find category');
			if( is_null( $jobdetails->get_sub_category() ) )
				throw new SJS_API_Missing_Field_Error('Failed to find sub_category');
			if( is_null( $jobdetails->get_region() ) )
				throw new SJS_API_Missing_Field_Error('Failed to find region');
			if( is_null( $jobdetails->get_hours_per_week() ) )
				throw new SJS_API_Missing_Field_Error('Failed to find hours_per_week');
			if( is_null( $jobdetails->get_title() ) )
				throw new SJS_API_Missing_Field_Error('Failed to find title');
			if( is_null( $jobdetails->get_description() ) )
				throw new SJS_API_Missing_Field_Error('Failed to find description');

			return $jobdetails;
		}		
		
		public function set_reference( $reference )
		{
			$this->reference = (int)$reference;
			return true;
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
	
	try {

		try {
			if( !isset($_GET['version']) )
				throw new SJS_API_Argument_Error('Missing required variable "version"');
			$api = new SJS_job_API( $_GET['version'] );
			
		} catch( SJS_API_Argument_Error $ex ) {
			header('Bad Request', false, 400);
			die( '<h1>'.$ex->getMessage().'</h1>' );
		}
	
		// Produce documentation
		if( count($_GET) == 1 )
		{
			// Say we're returning data without an error - but it's not what they asked for
			header('Non-Authoritative Information', false, 203);
			$api->show_documentation();
			exit();
		}

		if( !isset($_GET['format']) )
			throw new SJS_API_Argument_Error('Missing required variable "format"');
		
		$api->set_format( $_GET['format'], ( isset($_GET['callback'])?$_GET['callback']:null) );
		
		if( !isset($_GET['reference']) )
			throw new SJS_API_Argument_Error('Missing required variable "reference"');
		
		$api->set_reference( (int)$_GET['reference'] );
		
		$jobdetails = $api->parse_job_html( $api->get_job_content( $api->get_url() ) );
		
		// Remove unwanted fields
		if( isset($_GET['fields_to_return']) )
		{
			$unwantedfields = explode( ",", $_GET['fields_to_return'] );
			foreach($jobdetails as $field => $val)
				if( array_search( $field, $unwantedfields ) === false )
					unset($jobdetails->$field);
		}

		$api->output( $jobdetails );

	} catch( SJS_API_Argument_Error $ex ) {
		header('Bad Request', false, 400);
		echo '<h1>'.$ex->getMessage().'</h1><hr />';
		try { $api->show_documentation(); } catch( Exception $ex ) {}

		exit();
	} catch( Exception $ex ) {
		header('Internal Server Error', false, 500);
		echo '<h1>'.$ex->getMessage().'</h1>';
		var_dump ($ex);	
		exit();
	}
?>
