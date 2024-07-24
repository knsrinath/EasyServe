# EasyServe

![EasyServe](https://raw.githubusercontent.com/knsrinath/EasyServe/main/easyserve.png)

An easy-to-use Docker container for sharing files online. It's simple to set up but still lets you customize how it looks with different color options. Perfect for quickly sharing files without needing complex software.

### Currently available features:
| Environment Variable 	| Available options                       	|
|----------------------	|-----------------------------------------	|
| ALIGNMENT            	| center, left                            	|
| FOOTER_TEXT          	| Any text                                	|
| THEME                	| default, solarized, gruvbox, catppuccin 	|

### Installation
```bash
git clone https://github.com/knsrinath/easyserve.git
cd easyserve
docker compose build
docker compose up -d
```

### Credits
The php script is a modified version of [Minixed](https://github.com/lorenzos/Minixed/)