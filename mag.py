#
#   _ ____                        
#   (_)___ \                       
#   _  __) |_ __ ___   __ _  __ _ 
#   | ||__ <| '_ ` _ \ / _` |/ _` |
#   | |___) | | | | | | (_| | (_| |
#   |_|____/|_| |_| |_|\__,_|\__, |
#                             __/ |
#                             |___/ 
#
# Screen magnification using only X. Tuned for i3wm.
# Copyright (c) 2018 Niklas Kreer

from Xlib import X, display, Xutil
from Xlib.ext import randr

disp = display.window.xrandr_get_output_primary()._data
print(vars(disp))