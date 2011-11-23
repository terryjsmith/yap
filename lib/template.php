<?php

class Template {
	private $vars = array();
	private $layout = "layout.php";

	public function set($var, $value) {
		$this->vars[$var] = $value;
	}

	public function set_layout($render) {
		$this->layout = $render;
	}

	function get($var) {
		global $routing_controller;
                return($routing_controller->get_variable($var));
        }

        function post($var) {
		global $routing_controller;
                return($routing_controller->post_variable($var));
        }	
	
	public function render($tpl) {
		// Get the content
		ob_start();
		include($tpl);
		$this->vars['content'] = ob_get_clean();
		
		if($this->layout) {
			// Get the layout to decorate and output
			ob_start();
			include(VIEW_DIR . '/layouts/' . $this->layout);
			ob_end_flush();
		}
		else {
			header("Content-length: " . strlen($this->vars['content']));
			echo $this->vars['content']; die;
		}
	}
}

