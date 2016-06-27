<?php 
if(!class_exists('WeightPacketUtil')){
	class WeightPacketUtil{		
		public function pack_items_into_weight_box($items,	$max_weight){
			$boxes		=	array();
			$unpacked	=	array();
			foreach($items as $item){
				$fitted			=	false;
				$item_weight	=	$item['weight'];
				foreach($boxes as $box_key	=>	$box){
					if(($max_weight-$box['weight'])	>=	$item_weight){
						$boxes[$box_key]['weight']				=	$boxes[$box_key]['weight']+$item_weight;
						$boxes[$box_key]['items'][]				=	$item['data'];
						$fitted=true;
					}
				}
				if(!$fitted){
					if($item_weight	<=	$max_weight){
						$boxes[]	=	array(
							'weight'				=>	$item_weight,
							'items'					=>	array($item['data']),
						);
					}else{
						$unpacked[]	=	array(
							'weight'				=>	$item_weight,
							'items'					=>	array($item['data']),
						);
					}					
				}
			}
			$result	=	new WeightPackResult();
			$result->set_packed_boxes($boxes);
			$result->set_unpacked_items($unpacked);
			return $result;
		}
	}
}