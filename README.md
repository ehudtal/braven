# braven
The Braven public facing website

To pull code from server to local machine, run
```
ssh-add /path/to/ssh/key
rsync -avz root@<insertIPAddress>:/var/www/ /path/to/local/repo
```

To dump the database, login to phpmyadmin and click Export.  It will give you a localhost.sql file with all the commands to recreate the database.
