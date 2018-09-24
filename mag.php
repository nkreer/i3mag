<?php

include("xrandr-utility.php");

// Simple screen magnification for i3 using xrandr

$command = $argv[1];
$scriptlocation = __FILE__;

switch($command){
    case 'debug':
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
            file_put_contents($configpath, $i3config);
            echo "Configured i3mag.\n";
        } else {
            echo "Cancelled.";
            exit();
        }
        break;
    case '+':
        // Zoom in
        shell_exec("xrandr --output LVDS-1 --panning 1360x768 --mode 864x486");
        break;
    case '-':
        // Zoom out
        shell_exec("xrandr --output LVDS-1 --panning 1360x768 --mode 1360x768");
        break;
    default:
        echo "Invalid command.\n";
        exit();
        break;
}

function getUserInput($prompt){
    echo $prompt.": ";
    return trim(fgets(STDIN));
}