# progettoRAM - Source code
This folder contains the application's source code and corresponds to the web server's root.

The files are commented to give a brief description and describe some important points.

## Application structure
![Structure](../images/progettoRAM.svg)

The image above describes the internal file structure of the application, color-coded as:
* Blue: PHP scripts with front-end components
* Yellow: Back-end PHP scripts
* Orange: JavaScript files
* Green: CSS files

Relationships among files are color-coded as well:
* Black: link or redirect
* Blue: inclusion
* Red: asynchronous call

The inclusion relationships for `general.php` are not shown, as it is used for all PHP scripts.

## Deploy and files to add/modify
In order for the application to run correctly some settings are required after creating a web server (using for example Apache) with root folder `src`.

### server_conf.json
In a folder (that should not be accessible to the server) there must be the `server_conf.json` file, containing information about the system in the form
```
{
	"dbuser": "$USERNAME",
	"dbpass": "$PASSWORD",
	"dbname": "progettoRAM"
}
```
With the correct credentials for the MySQL user.

### [general.php](libraries/general.php)
`general.php` is the main file of the application and might need modifications at constants
* `6 - CONF_PATH`: Path to `server_conf.json`
* `7 - LOG_PATH`: Path to the folder to store the logs

More constants are set to the paths defined by Composer (so they shouldn't require modifications):
* `9 - JQUERY_PATH`: jQuery source file
* `11 - BOOTSTRAP_CSS_PATH`: Bootstrap's CSS source file
* `12 - BOOTSTRAP_JS_PATH`: Bootstrap's JavaScript source file
* `14 - FITTY_PATH`: Fitty's JavaScript's source file
* `15 - PLOTLY_PATH`: plotly.js source file

### [lib_stat.php](libraries/lib_stat.php)
In the statistical section's library file the value `CORRELATION_THRESH` can be modified to change the number of test results considered significant.
