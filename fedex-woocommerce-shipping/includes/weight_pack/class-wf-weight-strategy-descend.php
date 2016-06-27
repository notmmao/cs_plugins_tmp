<?php
if(!class_exists('WeightPackDescend')){
	class WeightPackDescend extends WeightPackStrategy{
		public function __construct(){
			parent::__construct();
		}
		
		public function pack_items(){
			$items=$this->get_packable_items();
			usort($items,	array($this,	'sort_items'));
			$result	=	$this->pack_util->pack_items_into_weight_box($items,	$this->get_max_weight());
			$this->set_result($result);
		}
		
		private function sort_items($a,	$b){
			$weight_a	=	floatval($a['weight']);
			$weight_b	=	floatval($b['weight']);
			if ($weight_a == $weight_b) {
				return 0;
			}
			return ($weight_a < $weight_b) ? +1 : -1;
		}
	}
}