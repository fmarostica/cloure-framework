<?php
	/**
	 * This is the console script for the Cloure Framework. All methods and properties are alphabetically order, so try to keep this methodology
	 * 
	 * @author Franco Marostica <franco@grupomarostica.com>
	 * @license MIT
	 */
	
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
						$this->print_header();
					}
					else {
						$this->cprint("The argument ".$argv[1]." isn't valid", "red", "");
					}
				} else {
					if($argv[1]=="make"){
						$option = isset($argv[2]) ? $argv[2] : ""; 
						$this->make($option);
					}
				}
			}
		}
		
		/**
		 * Prints in console with colors
		 *
		 * @param string $string
		 * @param string $color
		 * @param string $bg
		 * @return void
		 */
		private function cprint($string, $color="", $bg=""){
			$colored_string = "";

            // Check if given foreground color found
            if (isset($this->foreground_colors[$color])) {
                $colored_string .= "\033[" . $this->foreground_colors[$color] . "m";
            }
            // Check if given background color found
            if (isset($this->background_colors[$bg])) {
                $colored_string .= "\033[" . $this->background_colors[$bg] . "m";
            }

            // Add string and end coloring
			$colored_string .=  $string . "\033[0m";
			
            print($colored_string);
		}

		/**
		 * The make command
		 *
		 * @param [type] $option
		 * @return void
		 */
		private function make($option){
			if($option!=""){
				switch ($option) {
					case 'controller':
						$path = isset($this->args[3]) ? $this->args[3] : "";
						$this->make_controller($path);
						break;
					default:
						$this->cprint("The option $option is invalid", "red", "");
						break;
				}
			} else {
				$this->cprint("You must provide an option", "red", "");
			}
		}

		/**
		 * Make controller command
		 *
		 * @param string $path
		 * @return void
		 */
		function make_controller($path){
			$path = $this->rmkdir($path);

			if($path!==false){
				$controllerName = basename($path["path"]);
				$namespace = $path["namespace"];
				$path = $path["path"].".php";

				file_put_contents($path,"<?php\n", FILE_APPEND);
				file_put_contents($path,"\tnamespace App\\".$namespace.";\n\n", FILE_APPEND);
				file_put_contents($path,"\tclass $controllerName extends Controller{\n", FILE_APPEND);
				file_put_contents($path,"\t\t//\n", FILE_APPEND);
				file_put_contents($path,"\t}\n", FILE_APPEND);
				file_put_contents($path,"?>", FILE_APPEND);
				$this->cprint($controllerName." was successfully created!", "green", "");
			}
		}

		/**
		 * Create folders recursively
		 *
		 * @param string $path
		 * @return mixed
		 */
		private function rmkdir($path){
			$path = str_replace("\\", "/", $path);
			$path = "Controllers/".$path;
			$path = explode("/",$path);
			$filename = $path[count($path)-1];

			if(count($path)>1){
				$finalpath = "";
				//loop into folders
				for ($i=0; $i < (count($path)-1); $i++) { 
					$finalpath.=$path[$i]."\\";

					if(!file_exists($finalpath)){
						$this->cprint("Creating dir: ", "yellow");
						$this->cprint($path[$i]."\n", "");
						mkdir($finalpath);
					}
				}

				$namespace  = rtrim($finalpath, "\\");
				$finalpath.=$filename;

				return ["path"=>$finalpath, "namespace"=>$namespace];
			} else {
				$this->cprint("You must provide a path ", "red", "");
				return false;
			}
		}

		/**
		 * Prints the header containing framework version  
		 *
		 * @return void
		 */
		function print_header(){
			$this->cprint("Cloure framework", "yellow", "");
			$this->cprint(" v1.0.1", "", "");
			$this->cprint("\n\n");
		}

		/**
		 * Print main help in console
		 *
		 * @return void
		 */
		function print_main_help(){
			$this->print_header();
			$this->cprint("Use:", "green", "");
			$this->cprint("type php cloure command [options] [arguments] to execute a command\n");
			$this->cprint("type php cloure ");
			$this->cprint("command", "yellow");
			$this->cprint("option", "cyan");
			$this->cprint(" -h for help \n\n");
			$this->cprint("Available commands:", "green", "");
			
			if(count($this->commands)==0){
				$this->cprint("\n\nOh God!. There aren't available commands!", "", "red");
			} else {
				foreach ($this->commands as $command) {
					$this->cprint("  ".$command["name"], "yellow", "");
					$options = $command["options"];
					if(count($options)>0){
						$this->cprint("       Available options", "white", "");
						$this->cprint("       -----------------", "white", "");
					}
					foreach ($options as $option) {
						$this->cprint("       ".$option["name"], "cyan", "");
					}
				}
			}
		}
    }
    
?>