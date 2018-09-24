# i3mag

A very simple screen magnification solution for use with [i3wm](https://i3wm.org/) that makes use of xrandr's panning option. And yes, it's a PHP script. So make sure you have that installed.

## Getting started

Installation of i3mag is easy. It can add itself to your i3 configuration automatically so you don't have to figure this stuff out. Just navigate to your favourite place to install things and run the following commands (but remember to change the path to that of your own i3 config): 

```
git clone https://github.com/nkreer/i3mag
cd i3mag
php mag.php install /path/to/your/i3/config
php mag.php init
```

It'll create the shortcuts ``$mod+plus`` for zooming in and ``$mod+minus`` for zooming out. 