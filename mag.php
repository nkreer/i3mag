<?php

include("xrandr-utility.php");

// Simple screen magnification for i3 using xrandr

$command = $argv[1];
$scriptlocation = __FILE__;

switch($command){
    case 'debug':
        // Output the parsed xrandr information for debugging
        $output = new Output();
        var_dump($output);

        $config = json_decode(file_get_contents(__DIR__."/display.json"), true);
        $display = $output->displays[$config["name"]];
        var_dump(getResolutionsAspectRatio($display));
        break;
    case 'install':
        // Add the necessary configuration to i3 automatically
        if(!empty($argv[2])){
            $configpath = $argv[2];
        } else {
            echo "Please supply the path to your i3 config.\n";
            exit();
        }

        echo "Automatically configuring i3...\n";
        $prompt = getUserInput("This is going to mess with your i3 configuration in ".$configpath.". Proceed with caution! Would you like to continue? (y/N)");

        // Proceed with installation
        if(strtolower($prompt) === "y"){
            $i3config = file_get_contents($configpath);
            $i3config .= "\n# Auto-configuration for i3mag\n";
            // Add mod and - for decreasing magnification level
            $i3config .= 'bindsym $mod+minus exec php '.$scriptlocation.' -'."\n";
            // Add mod and + for increasing magnification level
            $i3config .= 'bindsym $mod+plus exec php '.$scriptlocation.' +'."\n";
            // Initialise on startup
            $i3config .= 'exec php '.$scriptlocation." init";
            file_put_contents($configpath, $i3config);
            echo "Configured i3mag. Restarting i3...\n";
            shell_exec("i3-msg restart");
        } else {
            echo "Cancelled.";
            exit();
        }
        break;
    case 'init':
        // Initialise i3mag when i3 starts
        echo "Initialising i3mag...\n";
        $information = new Output();

        // Get the primary monitor
        foreach($information->displays as $display){
            if($display->isPrimary){
                $primaryMonitor = $display;
                break; // Cancel, we have the primary screen
            }
        }

        if(!isset($primaryMonitor)){
            echo "Couldn't find the primary display. Aborting.\n";
            exit();
        }

        $display = [];
        $display["name"] = $primaryMonitor->name;
        $display["baseResolution"] = $primaryMonitor->resolution;
        $display["state"] = 0;
        $display = json_encode($display);
        file_put_contents(__DIR__."/display.json", $display);
        break;
    case '+':
        // Zoom in
        $config = json_decode(file_get_contents(__DIR__."/display.json"), true);

        $xrandr = new Output();
        $base = getBaseResolution($config);
        $modes = getResolutionsAspectRatio($xrandr->displays[$config["name"]]);

        $state = $config["state"] + 1; // One step down
        
        if($state <= (count($modes)-1)){
            shell_exec("xrandr --output ".$base[0]." --panning ".$base[1]." --mode ".$modes[$state]);
            saveConfig($config, $state);
        } else {
            shell_exec("i3-nagbar -t warning -m 'Already on maximum zoom level.'");
        }
        break;
    case '-':
        // Zoom out
        $config = json_decode(file_get_contents(__DIR__."/display.json"), true);

        $base = getBaseResolution($config);
        $xrandr = new Output();
        $base = getBaseResolution($config);
        $modes = getResolutionsAspectRatio($xrandr->displays[$config["name"]]);

        $state = $config["state"] - 1;  // One step up.

        if($state >= 0){
            shell_exec("xrandr --output ".$base[0]." --panning ".$base[1]." --mode ".$modes[$state]);
            saveConfig($config, $state);
        } else {
            shell_exec("i3-nagbar -t warning -m 'Impossible to zoom out further.'");
        }
        break;
    default:
        echo "Invalid command.\n";
        exit();
        break;
}

function getBaseResolution($config){
    return [$config["name"], $config["baseResolution"]];
}

/**
* Get all modes that have the same aspect ratio as the given display and are smaller than the base resolution
*/
function getResolutionsAspectRatio(Display $display){
    $resolution = $display->resolution;
    $resolution = explode("x", $resolution);
    $baseAspect = $resolution[0]/$resolution[1]; // Calculate aspect ratio
    $baseProduct = array_product($resolution);
    $others = [];
    foreach($display->modes as $mode){
        $result = explode("x", $mode);
        $resultAspect = $result[0]/$result[1];
        $resultProduct = array_product($result);
        // Check for same aspect ratio and avoid adding things twice or larger than the base resolution
        if(round($baseAspect) === round($resultAspect) && $baseProduct >= $resultProduct && !in_array($mode, $others)){
            // We have a match!
            $others[] = $mode;
        }
    }
    return $others;
}

function saveConfig($config, $currentState){
    $config["state"] = $currentState;
    file_put_contents(__DIR__."/display.json", json_encode($config));
}

function getUserInput($prompt){
    echo $prompt.": ";
    return trim(fgets(STDIN));
}
