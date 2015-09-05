<?php

//	define( "SJS_API_VERSIONS", "0.1, 0.1.1, 0.1.2" );
	
	require_once 'class.SJS_API.php';
	require_once 'class.SJS_job.php';
	require_once 'class.SJS_job_list.php';
	require_once 'class.JSONP.php';
		
	try {

		try {
			if( !isset($_GET['version']) )
				throw new SJS_API_Argument_Error('Missing required variable "version"');
			$api = new SJS_JobList_API( $_GET['version'] );
			
		} catch( SJS_API_Argument_Error $ex ) {
		echo 'abc';
		echo 'abc';
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
		
		if( isset( $_GET['min_hourly_rate'] ) )
			$api->set_min_hourly_rate( $_GET['min_hourly_rate'] );

		if( isset( $_GET['q'] ) )
			$api->set_search_string( $_GET['q'] );

		if( isset($_GET['max_results']) )
			$api->set_max_results( $_GET['max_results'] );
		
		if( isset($_GET['category']) ) $api->set_category( $_GET['category'] );
		if( isset($_GET['job_type']) ) $api->set_job_type( $_GET['job_type'] );
		if( isset($_GET['region']) )   $api->set_region( $_GET['region'] );
		
		if( isset($_GET['start_at']) )
			$start_at = (int)$_GET['start_at'];
		else
			$start_at = 0;
		$end_at = $start_at + $api->get_max_results();

		$listedjobs = array();
		
		// Pages output 10 jobs per page, so step up in these increments
		$pg=($start_at/10);
		for( $i=$start_at; $i<$end_at; $i+=10 )
		{
			$pg++;
			$html = $api->get_joblist_content( $api->get_url().($pg>1?'&page='.$pg:'') );
			try {
				$thesejobs = $api->parse_joblist_html( $html );
				foreach( $thesejobs as $job ) $listedjobs[] = $job;
			} catch( SJS_API_No_Results_Error $ex ) {
				break;
			}
			
		}

		// Remove unwanted fields
		if( isset($_GET['fields_to_return']) )
		{
			$unwantedfields = explode( ",", $_GET['fields_to_return'] );
			foreach( $listedjobs as &$job )
				foreach($job as $field => $val)
					if( array_search( $field, $unwantedfields ) === false )
						unset($job->$field);
		}
		
		$api->output( $listedjobs );
		
	} catch( SJS_API_Argument_Error $ex ) {
		header('Bad Request', false, 400);
		echo '<h1>'.$ex->getMessage().'</h1><hr />';
		try { $api->show_documentation(); } catch( Exception $ex ) {}

		exit();
	} catch( SJS_API_No_Results_Error $ex ) {
		//header('No Content', false, 204);
		$api->output( array() );

		exit();
	} catch( Exception $ex ) {
		header('Internal Server Error', false, 500);
		echo '<h1>'.$ex->getMessage().'</h1>';
		var_dump ($ex);	
		exit();
	}
?>
