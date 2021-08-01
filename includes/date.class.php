<?php

Class SystemDate {
	public static function datetime($datetime) {
		return date('Y-m-d H:i:s', strtotime($datetime));
	}
	
	public static function date($date) {
		return date('Y-m-d', strtotime($date));
	}
	
	public static function time($time) {
		return date('H:i:s', strtotime($time));
	}
}

Class AppDate {
	public static function datetime($datetime) {
		if ($datetime != '0000-00-00 00:00:00') {
			return date('d-m-Y g:i A', strtotime($datetime));
		} else {
			return '';
		}
	}
	
	public static function date($date) {
		if ($date != '0000-00-00') {
			return date('d-m-Y', strtotime($date));
		} else {
			return '';
		}
	}
	
	public static function time($time) {
		if(empty($time)){
			return '';
		}
		if ($time != '00:00:00') {
			return date('g:i A', strtotime($time));
		}else if ($time == '00:00:00'){
			return date('g:i A', strtotime($time));
		}else {
			return '';
		}
	}
}