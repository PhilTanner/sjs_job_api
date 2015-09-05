<?php

	class SJS_Job
	{
		public $category                 = null;
                public $date_application_closing = null;
                public $date_job_end             = null;
                public $date_job_listed          = null;
                public $date_job_start           = null;
                public $description              = null;
                public $hours_per_week           = null;
                public $id                       = null;
                public $job_type                 = null;
                public $pay_rate_from            = 0.00;
                public $pay_rate_to              = 0.00;
                public $requirements             = null;
                public $region                   = null;
                public $sub_category             = null;
                public $summary                  = null;
                public $total_price              = null;
                public $title                    = null;
                public $url                      = null;
                public $url_apply                = null;
		
		public function SJS_Job( $id )
		{
			$this->id = (int)$id;
			$this->set_url( 'http://www.sjs.co.nz/job/'.$this->get_id() );
			$this->set_url_apply( $this->get_url().'/apply' );
		}
		
		public function get_category(){ return $this->category; }
		public function get_date_application_closing(){ return $this->date_application_closing; }
		public function get_date_job_end(){ return $this->date_job_end; }
		public function get_date_job_listed(){ return $this->date_job_listed; }
		public function get_date_job_start(){ return $this->date_job_start; }
		public function get_description(){ return $this->description; }
		public function get_hours_per_week(){ return $this->hours_per_week; }
		public function get_id(){ return $this->id; }
		public function get_job_type(){ return $this->job_type; }
		public function get_pay_rate_from(){ return $this->pay_rate_from; }
		public function get_pay_rate_to(){ return $this->pay_rate_to; }
		public function get_region(){ return $this->region; }
		public function get_summary(){ return $this->get_summary; }
		public function get_sub_category(){ return $this->sub_category; }
		public function get_total_price(){ return $this->total_price; }
		public function get_title(){ return $this->title; }
		public function get_url(){ return $this->url; }
		public function get_url_apply(){ return $this->url_apply; }

		public function set_category( $str ){ $this->category = $str; return true; }
		public function set_date_application_closing( $str ){ $this->date_application_closing = date('c', strtotime($str)); return true; }
		public function set_date_job_end( $str ){ return $this->date_job_end = date('c', strtotime($str)); return true; }
		public function set_date_job_listed( $str ){ return $this->date_job_listed = date('c', strtotime($str)); return true; }
		public function set_date_job_start( $str ){ return $this->date_job_start = date('c', strtotime($str)); return true; }
		public function set_description( $str ){ $this->description = $str; return true; }
		public function set_hours_per_week( $str ){ $this->hours_per_week = (float)$str; return true; }
		public function set_job_type( $str ){ $this->job_type = $str; return true; }
		public function set_pay_rate_from( $str ){ $this->pay_rate_from = (float)$str; return true; }
		public function set_pay_rate_to( $str ){ $this->pay_rate_to = (float)$str; return true; }
		public function set_region( $str ){ $this->region = $str; return true; }
		public function set_summary( $str ){ $this->summary = $str; return true; }
		public function set_sub_category( $str ){ $this->sub_category = $str; return true; }
		public function set_total_price( $str ){ $this->total_price = (float)$str; return true; }
		public function set_title( $str ){ $this->title = $str; return true; }
		public function set_url( $str ){ $this->url = $str; return true; }
		public function set_url_apply( $str ){ $this->url_apply = $str; return true; }

	}
?>
