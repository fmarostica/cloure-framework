<?php
    namespace App\Core;

	use App\Core\Helpers;

    class Console
    {
		private $args = array();
        private $foreground_colors = array();
        private $background_colors = array();
		private $commands = [];

        public function __construct() {
			global $argv;
			$this->args = $argv;

			// Set up foreground colors
			$this->foreground_colors['black'] = '0;30';
			$this->foreground_colors['dark_gray'] = '1;30';
			$this->foreground_colors['blue'] = '0;34';
			$this->foreground_colors['light_blue'] = '1;34';
			$this->foreground_colors['green'] = '0;32';
			$this->foreground_colors['light_green'] = '1;32';
			$this->foreground_colors['cyan'] = '0;36';
			$this->foreground_colors['light_cyan'] = '1;36';
			$this->foreground_colors['red'] = '0;31';
			$this->foreground_colors['light_red'] = '1;31';
			$this->foreground_colors['purple'] = '0;35';
			$this->foreground_colors['light_purple'] = '1;35';
			$this->foreground_colors['brown'] = '0;33';
			$this->foreground_colors['yellow'] = '1;33';
			$this->foreground_colors['light_gray'] = '0;37';
			$this->foreground_colors['white'] = '1;37';

			// Set up background colors
			$this->background_colors['black'] = '40';
			$this->background_colors['red'] = '41';
			$this->background_colors['green'] = '42';
			$this->background_colors['yellow'] = '43';
			$this->background_colors['blue'] = '44';
			$this->background_colors['magenta'] = '45';
			$this->background_colors['cyan'] = '46';
			$this->background_colors['light_gray'] = '47';

			if(count($argv)==1){
				$this->print_main_help();
			} else {
				if(Helpers::starts_with($argv[1], "-")){
					if($argv[1]=="-h"){
						$this->print_main_help();
					}
					elseif($argv[1]=="-v"){
						$this->print_version();
					}
					else {
						$this->print($this->getColoredString("The argument ".$argv[1]." isn't valid", "red", "") . "\n");
					}
				} else {
					if($argv[1]=="make"){
						$option = isset($argv[2]) ? $argv[2] : ""; 
						$this->make($option);
					}
				}
			}
        }

        
        private function getColoredString($string, $foreground_color = null, $background_color = null) {
            $colored_string = "";

            // Check if given foreground color found
            if (isset($this->foreground_colors[$foreground_color])) {
                $colored_string .= "\033[" . $this->foreground_colors[$foreground_color] . "m";
            }
            // Check if given background color found
            if (isset($this->background_colors[$background_color])) {
                $colored_string .= "\033[" . $this->background_colors[$background_color] . "m";
            }

            // Add string and end coloring
            $colored_string .=  $string . "\033[0m";

            return $colored_string;
		}

		private function make($option){
			if($option!=""){
				switch ($option) {
					case 'controller':
						$path = isset($this->args[3]) ? $this->args[3] : "";
						$this->make_controller($path);
						break;
					default:
						print($this->getColoredString("The option $option is invalid", "red", "") . "\n");
						break;
				}
			} else {
				print($this->getColoredString("You must provide an option", "red", "") . "\n");
			}
		}

		function make_controller($path){
			if($path!=""){
				if(starts_with($path, "-")){
					$argument = $path;
					if($argument=="-h") print_make_controller_help();
				}
				else {
					print($this->getColoredString("Arg", "red", "") . "\n");
				}
			} else {
				print($this->getColoredString("You must provide a path ", "red", "") . "\n");
			}
		}

		/**
		 * Print the header on CLI
		 */
		function print_header(){
			print($this->getColoredString("Cloure framework", "yellow", ""));
			print($this->getColoredString(" v1.0.1", "", ""));
			print("\n\n");
		}

		/**
		 * Print version information
		 */
		function print_version(){
			$this->print($this->getColoredString("Cloure framework", "yellow", ""));
			$this->print($this->getColoredString(" v1.0.1", "", ""));
			$this->print("\n\n");
		}

		/**
		 * 
		 */
		function print_main_help(){
			$this->print_header();
			print($this->getColoredString("Use:", "green", "") . "\n");
			print("type php cloure command [options] [arguments] to execute a command\n");
			print("type php cloure ".$this->getColoredString("command", "yellow")." ".$this->getColoredString("option", "cyan")." -h for help \n\n");
			print($this->getColoredString("Available commands:", "green", "") . "\n");
			
			if(count($this->commands)==0){
				print($this->getColoredString("\n\nOh God!. There aren't available commands!", "", "red"));
			} else {
				foreach ($this->commands as $command) {
					print($this->getColoredString("  ".$command["name"], "yellow", "")."\n");
					$options = $command["options"];
					if(count($options)>0){
						print($this->getColoredString("       Available options", "white", "")."\n");
						print($this->getColoredString("       -----------------", "white", "")."\n");
					}
					foreach ($options as $option) {
						print($this->getColoredString("       ".$option["name"], "cyan", "")."\n");
					}
				}
			}
		}

		
    }
    
?>