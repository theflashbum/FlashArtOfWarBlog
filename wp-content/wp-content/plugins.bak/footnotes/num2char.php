<?php

/*
 * This file is part of WP-Footnotes a plugin for WordPress
 * Copyright (C) 2007 Simon Elvery
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

function swas_convert_num ($num, $style){
	switch ($style) {
		case 'lower-roman' :
			return swas_2roman($num, 'lower');
		case 'upper-roman' :
			return swas_2roman($num);
		case 'lower-alpha' :
			return swas_2alpha($num, 'lower');
		case 'upper-alpha' :
			return swas_2alpha($num);
	}
}

function swas_2roman($num, $case= 'upper'){
	$num = (int) $num;
	$conversion = array(1=>'I', 5=>'V', 10=>'X', 50=>'L', 100=>'C', 500=>'D', 1000=>'M');
	$roman = '';
	
	for ($i = 1000; floor($i)>0; $i=$i/10){
		switch (floor($num/$i)) {
			case 1 :
				$roman .= $conversion[$i];
				break;
			case 2 :
				$roman .= $conversion[$i].$conversion[$i];
				break;
			case 3 :
				$roman .= $conversion[$i].$conversion[$i].$conversion[$i];
				break;
			case 4 :
				$range = $i*5;
				$num2 = '4';
				
				while(strlen($num2) < strlen($range)){
					$num2 .= '0';
				}
				$in_front = $range-$num2;
				$roman .= $conversion[$in_front].$conversion[$range];
				$num = $num - $num2;
				
				break;
			case 5 :
				$roman .= $conversion[$i*5];
				break;
			case 6 :
				$roman .= $conversion[$i*5].$conversion[$i];
				break;
			case 7 :
				$roman .= $conversion[$i*5].$conversion[$i].$conversion[$i];
				break;
			case 8 :
				$roman .= $conversion[$i*5].$conversion[$i].$conversion[$i].$conversion[$i];
				break;
			case 9 :
				$range = $i*10;
				$num2 = '9';
				for ($j=1; $j<strlen($num); $j++){
					if (substr($num, $j, 1) == '9') {
						$num2 .= '9';
					}elseif (substr($num, $j, 1) == '5') {
						$num2 .= '5';
						break;
					}else{
						break;
					}
				}
				while(strlen($num2) < strlen($range)-1){
					$num2 .= '0';
				}
				$in_front = $range-$num2;
				if ($range/$in_front > 10){
					$num2 = '9';
					while(strlen($num2) < strlen($range)-1){
						$num2 .= '0';
					}
					$in_front = $range-$num2;
				}				
				$roman .= $conversion[$in_front].$conversion[$range];
				$num = $num - $num2;
				break;
		}
		// Take away what we've already dealt with
		$num = $num - $i*floor($num/$i);
	}
	if ($case == 'lower') $roman = strtolower($roman);
	return $roman;
}

function swas_2alpha($num, $case='upper'){
	$j = 1;
	for ($i = 'A'; $i <= 'ZZ'; $i++){
		if ($j == $num){
			if ($case == 'lower')
				return strtolower($i);
			else
				return $i;
		}
		$j++;
	}
	
}
?>