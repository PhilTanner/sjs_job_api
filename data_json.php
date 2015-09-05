<?php
	define( "SJS_API_VERSIONS", "0.1" );
	
	require_once 'class.JSONP.php';
	require_once 'class.SJS_API.php';	
	require_once 'class.SJS_data.php';

	
	try {

		try {
			if( !isset($_GET['version']) )
				throw new SJS_API_Argument_Error('Missing required variable "version"');
			$api = new SJS_data_API( $_GET['version'] );
			
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
		
		if( !isset($_GET['return']) )
			throw new SJS_API_Argument_Error('Missing required variable "return"');
		
		$api->set_format( $_GET['format'], ( isset($_GET['callback'])?$_GET['callback']:null) );
		
		$return = explode( ",", $_GET['return'] );
		$data = '';
		foreach( $return as $wanted )
		{
			switch( $wanted )
			{
				case 'region':
					$data = $api->get_regions();
					break;
				case 'category':
					$data = $api->get_work_category();
					break;
				case 'job_types':
					$data = $api->get_job_type();
					break;
				default:
					throw new SJS_API_Argument_Error('Unrecognised return variable option "'.$wanted.'"');
			}
		}
		//var_dump($data);
		$api->output( $data );

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
