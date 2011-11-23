<?php

class errorController extends Controller {
	function indexHandler() {
		header("HTTP/1.1 404 Not Found");
		$this->vars['title'] = 'Error';
	}
}

