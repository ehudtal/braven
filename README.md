# braven
The Braven public facing website

##Pulling the code
To pull code from server to local machine, run
```
ssh-add /path/to/ssh/key
rsync -avz root@<insertIPAddress>:/var/www/ /path/to/local/repo
```

To dump the database, login to phpmyadmin and click Export.  It will give you a localhost.sql file with all the commands to recreate the database.

##Editing the Theme
* It's best to first get acquainted with [Wordpress's function reference](https://developer.wordpress.org/reference/functions/).
* All custom functions should be under the functions.php file.
* Custom function names should start with "braven_" to avoid confilct with plug-ins and wordpress core functions.
* If user-facing text must be hard-coded into a template (rather than called in from a post/page/custom post type), always wrap it in a localization-friendly wrapper using `braven` as the localization handle: `__('your string here', 'braven')`. 
* When using jQuery, wrap your code with the following to avoid conflicting use of the $ symbol: 
``` javascript
( function( $ ) { 
  // Use regular jQuery $ syntax here
} )( jQuery );
```

##Troubleshooting Wordpress
* The most common reason things break is conflict with or between plugins. deactivate all plugins and re-activate them one by one to isolate the offender. 
* Wordpress support forums and Stack Exchange are highly likely to offer a solution.
