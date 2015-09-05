<?php

	define( "SJS_API_VERSIONS", "0.1, 0.1.1, 0.1.2" );
	
	require_once 'class.SJS_API.php';
	require_once 'class.SJS_job.php';
	require_once 'class.JSONP.php';
		
	class SJS_JobList_API extends SJS_API
	{
		public $max_results       = 10;
		public $min_hourly_rate   = 0.0;
		public $search_string     = '';
		public $start_at          = 0;
		public $region            = null;
		public $job_type          = null;
		public $category          = null;
		
                public $job_url           = 'search/jobs';
                public $base_domain       = 'http://www.sjs.co.nz/';
	
		public function SJS_JobList_API( $version )
		{
			switch($version)
			{
				case "0.1":
				case "0.1.1":
				case "0.1.2":
					$this->supported_formats = "json,jsonp";
					$this->max_results = 10;
					if( $_SERVER['REQUEST_METHOD'] != 'GET' )
						throw new SJS_API_Argument_Error('Only GET request method is allowed for this version');
					break;
				default:
					throw new SJS_API_Argument_Error('Variable "version" must be one of "'.SJS_API_VERSIONS.'"');
					break;
			}
			$this->version = $version;
		}
		
		public function get_joblist_content( $url )
		{
			$response = $this->get_content( $url );
			
			$pagecontent = $matches = array();
			preg_match('/div class="search-results">(.*)<div class="pagination-centered">/mis', $response, $pagecontent );

			if( count( $pagecontent ) )
				return $pagecontent[1];
			else 
				throw new SJS_API_No_Results_Error( 'Cannot find search results container.' );
				
		}
		
		public function parse_joblist_html( $html )
		{
			$listedjobs       = array();
			
			preg_match_all('/<div class="search-result">(.*)<div class="search-result-footer/Uis', $html, $jobs);
			
			if( count( $jobs ) )
			{
				foreach( $jobs[1] as $job )
				{
					if( isset( $this->max_results ) && $this->max_results && count( $listedjobs ) >= $this->max_results )
						break;
					
					$jobdetails = array();
					
					//preg_match_all('/<div class="field-label">(.*)<\/div>/Uis', $job, $labels);
					//preg_match_all('/class="field-item even">(.*)<\/div>/Uis', $job, $values);

					preg_match('/<a href="\/job\/([\d].*)"/Uis', $job, $id);
					if( count( $id ) && (int)$id[1] )
						$jobdetails = new SJS_Job((int)$id[1]);
					else throw new SJS_API_Missing_Field_Error('Unable to find job ID');
					
					preg_match('/<h2.*><a href="(.*)">(.*)<\/a><\/h2>/Uis', $job, $titles);
					if( count( $titles ) && strlen(trim($titles[2])))
						$jobdetails->set_title( trim($titles[2]) );
					else	throw new SJS_API_Missing_Field_Error('Failed to find job title');
					
                                        preg_match('/<div class="listed-date">Listed: (.*)<\/div>/Uis', $job, $dates);
                                        if( count( $dates ) && strlen(trim($dates[1])))
                                        {
                                                // All this is needed to help PHP understand a d/m/Y format....
                                                $datearr = date_parse_from_format ( 'd/m/Y' , $dates[1] );
                                                if( !$datearr['year'] )
                                                        throw new SJS_API_Parse_Error('Failed to parse '.$key.' "'.$dates[1].'"');
                                                $date = date_create();
                                                $date->setDate( $datearr['year'], $datearr['month'], $datearr['day'])->setTime( 0,0,0 );
                                                $jobdetails->set_date_job_listed( $date->format('c') );
                                        }
                                        else    throw new SJS_API_Missing_Field_Error('Failed to find listed date');

                                        preg_match('/<div class="date start-date"><span class="date-label">Start date:<\/span>(.*)<\/div>/Uis', $job, $dates);
                                        if( count( $dates ) && strlen(trim($dates[1])))
                                        {
						if( $dates[1] == 'ASAP' )
							$jobdetails->set_date_job_start( SJS_API_DATE_ASAP );
						else
						{
	                                                // All this is needed to help PHP understand a d/m/Y format....
        	                                        $datearr = date_parse_from_format ( 'd/m/Y' , $dates[1] );
        	                                        if( !$datearr['year'] )
        	                                                throw new SJS_API_Parse_Error('Failed to parse '.$key.' "'.$dates[1].'"');
        	                                        $date = date_create();
        	                                        $date->setDate( $datearr['year'], $datearr['month'], $datearr['day'])->setTime( 0,0,0 );	
        	                                        $jobdetails->set_date_job_start( $date->format('c') );
						}
                                        }

                                        preg_match('/<div class="date end-date"><span class="date-label">End date:<\/span>(.*)<\/div>/Uis', $job, $dates);
                                        if( count( $dates ) && strlen(trim($dates[1])))
                                        {
						if( $dates[1] == 'Ongoing' )
							$jobdetails->set_date_job_end( SJS_API_DATE_ONGOING );
						else
						{
	                                                // All this is needed to help PHP understand a d/m/Y format....
        	                                        $datearr = date_parse_from_format ( 'd/m/Y' , $dates[1] );
        	                                        if( !$datearr['year'] )
        	                                                throw new SJS_API_Parse_Error('Failed to parse '.$key.' "'.$dates[1].'"');
        	                                        $date = date_create();
        	                                        $date->setDate( $datearr['year'], $datearr['month'], $datearr['day'])->setTime( 0,0,0 );	
        	                                        $jobdetails->set_date_job_end( $date->format('c') );
						}
                                        }

                                        preg_match('/<tr><th>Region:<\/th><td>(.*), (.*)<\/td><\/tr>/Uis', $job, $field);
                                        if( count( $field ) && strlen(trim($field[1])))
                                        {
						unset($field[0]);
						$jobdetails->set_region( array_values($field) );
                                        }
                                        preg_match('/<tr><th>Regions:<\/th><td>(.*)<\/td><\/tr>/Uis', $job, $field);
                                        if( count( $field ) && strlen(trim($field[1])))
                                        {
						$region = explode('<br />', $field[1]);
						foreach( $region as &$v ) $v = explode(', ', $v);
						$jobdetails->set_region( $region );
                                        }

                                        preg_match('/<tr><th>Category:<\/th><td>(.*), (.*)<\/td><\/tr>/Uis', $job, $field);
                                        if( count( $field ) && strlen(trim($field[1])))
                                        {
						$jobdetails->set_category( $field[1] );
						$jobdetails->set_sub_category( $field[2] );
                                        }

                                        preg_match('/<div class="detail details-dollars">(.*): \$(.*)<\/div>/Uis', $job, $field);
                                        if( count( $field ) && strlen(trim($field[1])))
                                        {
						if( $field[1] == 'Payrate' )
						{
							$jobdetails->set_pay_rate_from( $field[2] );
							$jobdetails->set_pay_rate_to( $field[2] );
						} elseif( $field[1] == 'Total' ){
							$jobdetails->set_total_price( $field[2] );
						}
                                        }

                                        preg_match('/<div class="detail details-hours">Hours: (.*)<\/div>/Uis', $job, $field);
                                        if( count( $field ) && strlen(trim($field[1])))
                                        {
						$jobdetails->set_hours_per_week( $field[1] );
                                        }

                                        preg_match('/<tr><th>Job type:<\/th><td>(.*)<\/td><\/tr>/Uis', $job, $field);
                                        if( count( $field ) && strlen(trim($field[1])))
                                        {
						$jobdetails->set_job_type( $field[1] );
                                        }

                                        preg_match('/<\/div><\/div><div class="clearfix"> <\/div>(.*)<div class="readmore-link">/Uis', $job, $field);
                                        if( count( $field ) && strlen(trim($field[1])))
                                        {
						$jobdetails->set_summary( $field[1] );
                                        }



/*
							case 'closing_date':
							case 'application_closing_date':
								if( !strtotime( $values[1][$index] ) )
									throw new SJS_API_Parse_Error('Failed to parse '.$key.' "'.$values[1][$index].'"');
								$jobdetails->set_date_application_closing( date('c', strtotime( $values[1][$index] )) );
								break;
							case 'relisted_date':
								if( strtoupper(trim( $values[1][$index])) == 'N/A' ) break;
								 // All this is needed to help PHP understand a d/m/Y format....
                                                                $datearr = date_parse_from_format ( 'd/m/Y' , $values[1][$index] );
                                                                if( !$datearr['year'] )
                                                                        throw new SJS_API_Parse_Error('Failed to parse '.$key.' "'.$values[1][$index].'"');
                                                                $date = date_create();
                                                                $date->setDate( $datearr['year'], $datearr['month'], $datearr['day'])->setTime( 0,0,0 );
                                                                $jobdetails->set_date_job_listed( $date->format('c') );
                                                                break;
							default:
								throw new SJS_API_Parse_Error('Unknown field "'.$key.'"');
						}
					}
*/
					
					if( is_null( $jobdetails->get_category() ) )
{ var_dump($job);						throw new SJS_API_Missing_Field_Error('Failed to find category');}
//					if( is_null( $jobdetails->get_sub_category() ) )
//						throw new SJS_API_Missing_Field_Error('Failed to find sub_category');
					if( is_null( $jobdetails->get_region() ) )
{ var_dump($job);						throw new SJS_API_Missing_Field_Error('Failed to find region'); }
					if( is_null( $jobdetails->get_hours_per_week() ) )
{ var_dump($job);						throw new SJS_API_Missing_Field_Error('Failed to find hours_per_week'); }

					$listedjobs[] = $jobdetails;
					
				}
			}
			return $listedjobs;
		}
		
		public function get_max_results()
		{
			return $this->max_results;
		}
		
		public function get_url()
		{
			$url_vars = array();
			if( $this->min_hourly_rate )
				$url_vars[] = 'rate='.(float)$this->min_hourly_rate;
			
			if( !is_null($this->region) )
				$url_vars[] = 'regions='.urlencode($this->region);
/*
			if( !is_null($this->job_type) )
				$url_vars[] = 'orfilter[field_job_type][0]="'.$this->job_type.'"';
			if( !is_null($this->category) )
				$url_vars[] = 'taxonomy[field_job_category][0]="'.$this->category.'"';
			var_dump($this->base_domain.$this->job_url.(strlen(trim($this->search_string))?'/'.$this->search_string:'').'?'.implode('&',$url_vars));
*/
			return $this->base_domain.$this->job_url.(strlen(trim($this->search_string))?'/'.$this->search_string:'').'?'.implode('&',$url_vars);
		}
		
		
		public function set_max_results( $amount )
		{
			if( !(int)$amount || (int)$amount > 30 )
				throw new SJS_API_Argument_Error('The "max_results" variable you requested is invalid');
			$this->max_results = (int)$amount;
			return true;

		}
		
		public function set_min_hourly_rate( $amount )
		{
			$this->min_hourly_rate = (float)$amount;
			return true;
		}
		
		public function set_category( $category ){if( $category != 'null' )  $this->category = urlencode(trim($category)); return true; }
		public function set_job_type( $job_type ){ if( $job_type != 'null' ) $this->job_type = urlencode(trim($job_type)); return true; }
		public function set_region( $region ){ if( $region != 'null' ) $this->region = urlencode(trim($region)); return true; }
		
		public function set_search_string( $str )
		{
			 $this->search_string = urlencode(trim($str));
			 return true;
		}
		
		public function show_documentation()
		{
			echo '<html><body><h1>SJS API Documentation</h1><h2>Version '.$_GET['version'].'</h2>';
			echo '<h3>Changelog:</h3><ul>';
			switch( $this->version )
			{
				case "0.1.2":
					echo '<li>0.1.2 2014/05/28 - Added start_at and fields_to_return values</li>';
				case "0.1.1":
					echo '<li>0.1.1 2014/05/01 - Relisted_date field added</li>';
				case "0.1":
					echo '<li>0.1 2014/03/04 - OO Version with some error handling</li>';
					echo '<li>0.0 2014/02/24 - Initial release</li>';
					echo '</ul><h3>Inputs:</h3>';
					echo '<table border="1"><thead><tr><th>Name</th><th>Data type</th><th>Valid value(s)</th><th>Required</th><th>Description</th></tr></thead><tbody>';
					echo '<tr><td>version</td><td>string</td><td>'.SJS_API_VERSIONS.'</td><td>Yes</td><td>The version number of the API you are calling.</td></tr>';
					echo '<tr><td>format</td><td>string</td><td>'.SJS_API_FORMATS.'</td><td>Yes</td><td>What format you want the data returned in.</td></tr>';
					echo '<tr><td>callback</td><td>string</td><td></td><td>Yes if format=jsonp, otherwise No</td><td>The name of the callback function wrapper for JSONP queries.</td></tr>';
					echo '<tr><td>location</td><td>csv</td><td>System recognised</td><td>No</td><td>A list of SJS recognised place names.</td></tr>';
					echo '<tr><td>min_hourly_rate</td><td>float</td><td><strong>Default: 0.00</strong></td><td>No</td><td>Return jobs which will pay a minimum of this hourly rate (NZD).</td></tr>';
					echo '<tr><td>max_results</td><td>int</td><td>&lt;30<br /> <strong>Default: 10</strong></td><td>No</td><td>Maximum results to return.</td></tr>';
					echo '<tr><td>q</td><td>string</td><td>Any</td><td>No</td><td>Text strings to match against</td></tr>';
					echo '</tbody></table>';
					break;
				default:
					break;
			}
			echo '</body></html>';
		}

	}
	
?>
