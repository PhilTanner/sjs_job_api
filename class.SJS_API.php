<?php
	class SJS_Error                   extends Exception { /*
		// Redefine the exception so message isn't optional
		public function __construct($message, $code = 0, Exception $previous = null) 
		{
			// make sure everything is assigned properly
			parent::__construct($message, $code, $previous);
		}
		
		// custom string representation of object
		public function __toString() { return __CLASS__ . ": [{$this->code}]: {$this->message}\n"; }
		*/
	}
	class SJS_API_Error               extends SJS_Error {}
	class SJS_API_Argument_Error      extends SJS_API_Error {}
	class SJS_API_Retrieve_Error      extends SJS_API_Error {}
	class SJS_API_Parse_Error	  extends SJS_API_Error {}
	class SJS_API_Missing_Field_Error extends SJS_API_Parse_Error {}
	class SJS_API_No_Results_Error    extends SJS_API_Parse_Error {}
	
	define( "SJS_API_FORMATS",            "json,jsonp" );
	define( "SJS_API_DATE_ASAP",          "1970-01-01T00:00:01+12" );
	define( "SJS_API_DATE_NOT_SPECIFIED", "" );
	define( "SJS_API_DATE_ONGOING",       "1970-01-01T00:00:02+12" );

	class SJS_API
	{
		public $version           = null;
		public $supported_formats = SJS_API_FORMATS;
		public $format            = 'json';
		public $callback          = null;
		
		public function SJS_API( $version )
		{
			$this->version = $version;
		}
		
		public function get_content( $url )
		{
			$ch = curl_init( $url );
			
			curl_setopt($ch, CURLOPT_NOBODY,	 0 );
			curl_setopt($ch, CURLOPT_HEADER,	 0 );
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1 );
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt($ch, CURLOPT_FAILONERROR,    0 );
		
			$response     = curl_exec($ch);
			$responseInfo = curl_getinfo($ch);
			curl_close($ch);
		
			if( (int)$responseInfo['http_code'] == 200 )
			{
				$pagecontent = $matches = array();
				preg_match('/<body(.*)<\/body/mis', $response, $pagecontent );
				
				if( count( $pagecontent ) )
					return $pagecontent[1];
				else 
					throw new SJS_API_No_Results_Error( 'Cannot find details.' );
			} else var_dump($responseInfo);//throw new SJS_API_Retrieve_Error( $responseInfo );
		}
				
		public function set_format( $format, $callback = null )
		{
			$format = strtolower(trim($format));
			
			if( array_search( $format, explode(',', $this->supported_formats ) ) === false )
				throw new SJS_API_Argument_Error('Unsupported format. Variable "format" must be one of "'.$this->supported_formats.'"');

			$this->format = $format;

			if( $this->format == 'jsonp' && is_null($callback) )
				throw new SJS_API_Argument_Error('JSONP format requires an additional "callback" variable');
			if( $this->format == 'jsonp' )
			{
				if( !Jsonp::isValidCallback($callback) )
					throw new SJS_API_Argument_Error('The "callback" variable you requested is invalid');
				else 
					$this->callback = $callback;
			}

			return true;
		}
		
		public function output( $data )
		{
			switch( $this->format )
			{
				case 'json':
					header('Content-Type: application/json; charset=utf-8');
					die( json_encode($data) );
					break;
				case 'jsonp':
					header('Content-Type: application/javascript; charset=utf-8');
					// http://enable-cors.org/ - Allow anyone to access our domains for live/staging
					header("Access-Control-Allow-Origin: *");
					die( $this->callback.'('.json_encode($data).')' );
					break;
				default:
					throw new SJS_API_Error( 'Unknown output format: '.$this->format );
			}			
		}
		
		// All this is needed to help PHP understand a d/m/Y format....
		public function parseDMYdate( $DMYdate )
		{
			$DMYdate = strip_tags(trim($DMYdate));
			if( strtoupper($DMYdate) == 'ASAP' ) 
				return SJS_API_DATE_ASAP; 
			if( strtoupper($DMYdate) == 'NOT SPECIFIED' ) 
				return SJS_API_DATE_NOT_SPECIFIED; 
			if( strtoupper($DMYdate) == 'ONGOING' ) 
				return SJS_API_DATE_ONGOING; 
			$datearr = date_parse_from_format ( 'd/m/Y', $DMYdate );
			if( !$datearr['year'] )
				throw new SJS_API_Parse_Error('Failed to parse date "'.$DMYdate.'"');
			$date = date_create();
			$date->setDate( $datearr['year'], $datearr['month'], $datearr['day'])->setTime( 0,0,0 );
			return $date->format('c');
		}

		// Strip potentially dangerous information from user input 
		static function sanitise( $input, $fordatabase=false, $forcommandline=false, $forhtml=false, $allowabletags = '<a><b><blockquote><br><caption><em><h1><h2><h3><h4><h5><h6><hr><i><li><ol><p><q><strong><table><tbody><tfoot><td><th><thead><tr><ul>' )
		{
			$escapedinput = $input;
			if( $forhtml ) 
				$escapedinput = strip_tags($escapedinput, $allowabletags );
			if( $fordatabase ) 
				$escapedinput = mysql_real_escape_string( $escapedinput );
			if( $forcommandline ) 
				$escapedinput = escapeshellarg( $escapedinput );
			return $input;
		}
		
	}
?>
