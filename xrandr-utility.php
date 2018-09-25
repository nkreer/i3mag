<?php

/**
* Base class that parses and collects everything
*/
class Output{

    public $output;
    public $displays = [];

    public function __construct(){
        $this->output = shell_exec("xrandr");
        $this->parse();
    }

    /**
    * Parse the output of our xrandr command
    */
    private function parse(){
        $lines = explode("\n", $this->output);
        foreach($lines as $index => $line){
            $string = explode(" ", $line);
            // If we have a connected monitor, continue
            if($string[1] === "connected"){
                $monitor = new Display();
                $monitor->name = $string[0]; // Monitor name is the first thing we get from the output
                if($string[2] === "primary"){
                    $monitor->isPrimary = true;
                }
                $monitor->resolution = str_ireplace("+0+0", "", $string[3]);

                // Find all available modes
                $modes = $lines;
                for($i = 0; $i <= $index; $i++) unset($modes[$i]);
                foreach($modes as $mode){
                    // Check whether we have an actual mode listing
                    if(substr($mode, 0, 3) === "   "){
                        $listing = explode(" ", $mode);
                        $monitor->modes[] = $listing[3]; // Add the resolution to the modes array
                    } else {
                        break; // We no longer need this loop because we already have all the modes.
                    }
                }

                $this->displays[$monitor->name] = $monitor;
            }
        }
    }

}

/**
* Has all the information about a single display
*/
class Display{

    public $modes = [];
    public $name = "";
    public $isPrimary = false;
    public $resolution = "0x0";

}