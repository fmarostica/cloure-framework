<?php
    
    namespace App\Core;

    class Helpers{
        public static function starts_with($string, $search){
			$chars_count = strlen($search);
			if(substr($string, 0, $chars_count) === $search){
				return true;
			} else {
				return false;
			}
		}
    }

?>