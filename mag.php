<?php

include("xrandr-utility.php");

// Simple screen magnification for i3 using xrandr

$command = $argv[1];
$scriptlocation = __FILE__;

switch($command){
    case 'debug':
        // Output the parsed xrandr information for debugging
        var_dump(new Output());
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
        $display = json_encode($display);
        file_put_contents(__DIR__."/display.json", $display);
        break;
    case '+':
        // Zoom in
        $config = json_decode(file_get_contents(__DIR__."/display.json"), true);

        $base = getBaseResolution($config);

        shell_exec("xrandr --output ".$base[0]." --panning ".$base[1]." --mode 864x486");
        break;
    case '-':
        // Zoom out
        $config = json_decode(file_get_contents(__DIR__."/display.json"), true);

        $base = getBaseResolution($config);

        shell_exec("xrandr --output ".$base[0]." --panning ".$base[1]." --mode ".$base[1]);
        break;
    default:
        echo "Invalid command.\n";
        exit();
        break;
}

function getBaseResolution($config){
    return [$config["name"], $config["baseResolution"]];
}

function getUserInput($prompt){
    echo $prompt.": ";
    return trim(fgets(STDIN));
}