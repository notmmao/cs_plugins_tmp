<?php
if(!class_exists('WeightPackSimple')){
	class WeightPackSimple extends WeightPackStrategy{
		public function __construct(){
			parent::__construct();
		}
		
		public function pack_items(){
			$items=$this->get_packable_items();
			$boxes			=	array();
			$total_weight	=	0;
			foreach($items as $item){
				$total_weight	+=	$item['weight'];					
			}
			$max_weight	=	$this->get_max_weight();
			if(!$total_weight || !$max_weight){
				return false;
			}
			do{
				$pack_weight	=	($total_weight/$max_weight)>1?$max_weight:$total_weight;
				$boxes[]	=	array(
					'weight'	=>	$pack_weight
				);
				$total_weight	=	$total_weight-$pack_weight;
			}while(	$total_weight	);
			
			$result	=	new WeightPackResult();
			$result->set_packed_boxes($boxes);
			$this->set_result($result);
		}
	}
}