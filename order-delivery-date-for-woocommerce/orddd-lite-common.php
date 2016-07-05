<?php 

/**
 * orddd_common Class
 *
 * @class orddd_common
 */
class orddd_lite_common {
    
    /**
     * Return the date with the selected langauge in Appearance tab
     * 
     * @param string $delivery_date_formatted
     * @param string $delivery_date_timestamp
     * @return string
     */
	public static function delivery_date_lite_language( $delivery_date_formatted, $delivery_date_timestamp ) {
		global $orddd_lite_languages, $orddd_lite_languages_locale;
		$date_language = get_option( 'orddd_lite_language_selected' );
		if( $delivery_date_timestamp != '' ) {
            if( $date_language != 'en-GB' ) {
                $locale_format = $orddd_lite_languages[ $date_language ];
                $time = setlocale( LC_ALL, $orddd_lite_languages_locale[ $locale_format ] );
                $date_format = get_option( 'orddd_lite_delivery_date_format' );
                switch ( $date_format ) {
                    case 'd M, y':
                        $date_str = str_replace( 'd', '%d', $date_format );
                        $month_str = str_replace( 'M', '%b', $date_str );
                        $year_str = str_replace( 'y', '%y', $month_str );
                        break;
                    case 'd M, yy':
                        $date_str = str_replace( 'd', '%d', $date_format );
                        $month_str = str_replace( 'M', '%b', $date_str );
                        $year_str = str_replace( 'yy', '%Y', $month_str );
					    break;
                    case 'd MM, y':
                        $date_str = str_replace( 'd', '%d', $date_format );
                        $month_str = str_replace( 'MM', '%B', $date_str );
                        $year_str = str_replace( 'y', '%y', $month_str );
                        break;
                    case 'd MM, yy':
                        $date_str = str_replace( 'd', '%d', $date_format );
                        $month_str = str_replace( 'MM', '%B', $date_str );
                        $year_str = str_replace( 'yy', '%Y', $month_str );
                        break;
                    case 'DD, d MM, yy':
                        $day_str = str_replace( 'DD', '%A', $date_format );
                        $date_str = str_replace( 'd', '%d', $day_str );
                        $month_str = str_replace( 'MM', '%B', $date_str );
                        $year_str = str_replace( 'yy', '%Y', $month_str );
                        break;
                    case 'D, M d, yy':
                        $day_str = str_replace( 'D', '%a', $date_format );
                        $date_str = str_replace( 'd', '%d', $day_str );
                        $month_str = str_replace( 'M', '%b', $date_str );
                        $year_str = str_replace( 'yy', '%Y', $month_str );
                        break;
                    case 'DD, M d, yy':
				        $day_str = str_replace( 'DD', '%A', $date_format );
                        $date_str = str_replace( 'd', '%d', $day_str );
                        $month_str = str_replace( 'M', '%b', $date_str );
                        $year_str = str_replace( 'yy', '%Y', $month_str );
                        break;
                    case 'DD, MM d, yy':
                        $day_str = str_replace( 'DD', '%A', $date_format );
                        $date_str = str_replace( 'd', '%d', $day_str );
                        $month_str = str_replace( 'MM', '%B', $date_str );
                        $year_str = str_replace( 'yy', '%Y', $month_str );
                        break;
                    case 'D, MM d, yy':
				        $day_str = str_replace( 'D', '%a', $date_format );
                        $date_str = str_replace( 'd', '%d', $day_str );
                        $month_str = str_replace( 'MM', '%B', $date_str );
                        $year_str = str_replace( 'yy', '%Y', $month_str );
                        break;
                }
                                
                if( isset( $year_str ) ) {
                    $delivery_date_formatted = strftime( $year_str, $delivery_date_timestamp );
                }                
                setlocale( LC_ALL, 'en_GB.utf8' );
            }
        }
		return $delivery_date_formatted;
	}
	
	/** 
	 * Returns Delivery date for an order
	 * 
	 * @param int $order_id
	 * @return string
	 */
	public static function orddd_lite_get_order_delivery_date( $order_id ) {
	    global $orddd_lite_date_formats;
	    $data = get_post_meta( $order_id );
	    $field_date_label = get_option( 'orddd_lite_delivery_date_field_label' );
	    $delivery_date_formatted = $delivery_date_timestamp = '';
	    if ( isset( $data[ '_orddd_lite_timestamp' ] ) || isset( $data[ get_option( 'orddd_lite_delivery_date_field_label' ) ] ) ) {
	        if ( isset( $data[ '_orddd_lite_timestamp' ] ) ) {
	            $delivery_date_timestamp = $data[ '_orddd_lite_timestamp' ][ 0 ];
	        }
	        $delivery_date_formatted = '';
	        if ( $delivery_date_timestamp != '' ) {
	            $delivery_date_formatted = date( $orddd_lite_date_formats[ get_option( 'orddd_lite_delivery_date_format') ], $delivery_date_timestamp );
	        } else {
	            if ( array_key_exists( get_option( 'orddd_lite_delivery_date_field_label' ), $data ) ) {
	                //$delivery_date_replace = str_replace(","," ",$data[ get_option( 'orddd_lite_delivery_date_field_label' ) ][ 0 ]);
	                $delivery_date_timestamp = strtotime( $data[ get_option( 'orddd_lite_delivery_date_field_label' ) ][ 0 ] );
	                if ( $delivery_date_timestamp != '' ) {
	                    $delivery_date_formatted = date( $orddd_lite_date_formats[ get_option( 'orddd_lite_delivery_date_format' ) ], $delivery_date_timestamp );
	                }
	            } elseif ( array_key_exists( ORDDD_DELIVERY_DATE_FIELD_LABEL, $data ) ) {
	                $delivery_date_timestamp = strtotime( $data[ ORDDD_DELIVERY_DATE_FIELD_LABEL ][ 0 ] );
	                if ( $delivery_date_timestamp != '' ) {
	                    $delivery_date_formatted = date( $orddd_lite_date_formats[ get_option( 'orddd_lite_delivery_date_format' ) ], $delivery_date_timestamp );
	                }
	            }
	        }
	        $delivery_date_formatted = orddd_lite_common::delivery_date_lite_language( $delivery_date_formatted, $delivery_date_timestamp );
	    }
	    return $delivery_date_formatted;
	}
	
	
	/**
	 * Returns timestamp for the selected Delivery date
	 * 
	 * @param string $delivery_date
	 * @param string $date_format
	 * @param array $time_setting
	 * @return string
	 */
	
	public static function orddd_lite_get_timestamp( $delivery_date, $date_format ) {
	    $hour = 0;
	    $min = 1;
	    $date_str = '';
	    $m = $d = $y = 0;
	    if( $delivery_date != '' ) {
            switch ( $date_format ) {
                case 'mm/dd/y':
                    $date_arr = explode( '/', $delivery_date );
                    $m = $date_arr[ 0 ];
                    $d = $date_arr[ 1 ];
                    $y = $date_arr[ 2 ];
                    break;
                case 'dd/mm/y':
                    $date_arr = explode( '/', $delivery_date );
                    $m = $date_arr[ 1 ];
                    $d = $date_arr[ 0 ];
                    $y = $date_arr[ 2 ];
                    break;
                case 'y/mm/dd':
                    $date_arr = explode( '/', $delivery_date );
                    $m = $date_arr[ 1 ];
                    $d = $date_arr[ 2 ];
                    $y = $date_arr[ 0 ];
                    break;
                case 'dd.mm.y':
                    $date_arr = explode( '.', $delivery_date );
                    $m = $date_arr[ 1 ];
                    $d = $date_arr[ 0 ];
                    $y = $date_arr[ 2 ];
                    break;
                case 'y.mm.dd':
                    $date_arr = explode( '.', $delivery_date );
                    $m = $date_arr[ 1 ];
                    $d = $date_arr[ 2 ];
                    $y = $date_arr[ 0 ];
                    break;
                case 'yy-mm-dd':
                    $date_arr = explode( '-', $delivery_date );
                    $m = $date_arr[ 1 ];
                    $d = $date_arr[ 2 ];
                    $y = $date_arr[ 0 ];
                    break;
                case 'dd-mm-y':
                    $date_arr = explode( '-', $delivery_date );
                    $m = $date_arr[ 1 ];
                    $d = $date_arr[ 0 ];
                    $y = $date_arr[ 2 ];
                    break;
                case 'd M, y':
                    $date_str = str_replace( ',', '', $delivery_date );
                    break;
                case 'd M, yy':
                    $date_str = str_replace( ',', '', $delivery_date );
                    break;
                case 'd MM, y':
                    $date_str = str_replace( ',', '', $delivery_date );
                    break;
                case 'd MM, yy':
                    $date_str = str_replace( ',', '', $delivery_date );
                    break;
                case 'DD, d MM, yy':
                    $date_str = str_replace( ',', '', $delivery_date );
                    break;
                case 'D, M d, yy':
                    $date_str = str_replace( ',', '', $delivery_date );
                    break;
                case 'DD, M d, yy':
                    $date_str = str_replace( ',', '', $delivery_date );
                    break;
                case 'DD, MM d, yy':
                    $date_str = str_replace( ',', '', $delivery_date );
                    break;
                case 'D, MM d, yy':
                    $date_str = str_replace( ',', '', $delivery_date );
                    break;
            }
            if ( isset( $date_str ) && $date_str != '' ) {
	            $timestamp = strtotime( $date_str );
            } else {
                $timestamp = mktime( 0, 0, 0, $m, $d, $y );
            }
	    } else {
	        $timestamp = '';
	    }
	    return $timestamp;
	}
}
?>