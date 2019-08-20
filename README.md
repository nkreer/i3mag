# i3mag

A screen magnification solution for [i3wm](https://i3wm.org/) that uses xrandr's panning option. And yes, it's a PHP script. So make sure you have the required interpreter for that.

__Attention:__ I have a better solution for zooming in i3wm now, if you don't mind losing the ability to interact with your computer while being zoomed in. It's called [screenzoom.sh](https://gist.github.com/nkreer/cca0ef25077dfa8cbfbc2f84a59a498d) and uses screenshots and the image viewer [feh](https://github.com/nkreer/feh) for much faster and easier magnification. If your situation allows for it, be sure to check it out as well. Both that and i3mag may be used in combination.

## Who is this for?

Visually impaired people who'd like to use full screen magnification and the i3 tiling window manager. Don't try to use this for screencasts etc. as it changes your actual screen resolution when activated.  

## Getting started

Installation of i3mag is easy. It can add itself to your i3 configuration automatically so you don't have to figure this stuff out. Just navigate to your favourite place to install things and run the following commands (but remember to change the path to that of your own i3 config): 

```
git clone https://github.com/nkreer/i3mag
cd i3mag
php mag.php install /path/to/your/i3/config
php mag.php init
```

It'll create the shortcuts ``$mod+plus`` for zooming in and ``$mod+minus`` for zooming out. On i3 startup, it'll automatically initialise.

If you'd like to change i3's config yourself (e.g. for using custom keyboard shortcuts or not having i3mag start with the wm), use these options as a base and change them to your liking:

```
# Auto-configuration for i3mag
bindsym $mod+minus exec php mag.php -
bindsym $mod+plus exec php mag.php +
exec php mag.php init
```
