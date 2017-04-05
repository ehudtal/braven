#!/bin/bash

# Takes a dump of a compressed production Wordpress database 
# (as the one command line argument) and migrates it to a dev db.

if [ -z "$1" ]
then
  echo "No argument supplied.  Usage migrate_prod_db.bat <prod_db_file.gz>"
  exit 1;
fi

if [ -f $1 ]
then

  echo "Reading $1 and migrating it to a development database called braven-dev-db.sql.gz"
  db_dump_prod_file=$1
  db_dump_dev_file=braven-dev-db.sql

  # Turn it into a development worth DB, compress and add to S3 so that local dev environments can pull it.
  gzip -cd $db_dump_prod_file | sed -e "

    s/https:\/\/bebraven.org/http:\/\/braven.docker/g;
    s/https:\/\/www.bebraven.org/http:\/\/braven.docker/g;
    s/https:\/\/join.bebraven.org/http:\/\/join.docker/g;

    # Reset all salted passwords to test1234.  This new hash was created by adding 
    #     wp_set_password( 'password', 1 );
    # to functions.php, loading the page once, and copying the salted hash out of the db.
    # This only works with the NONCE, etc in the docker-compose/config/wp-config.php.
    # Note: this regex says find anything starting with $P$B and all characters up until the first '
    # and then replace it with $P$BDdHpx9r6U6Y.UpQ1SIU.7/f.Tf0841 which is the salted hash of test1234
    s|\$P\$B[^']*|\$P\$BDdHpx9r6U6Y.UpQ1SIU.7/f.Tf0841|g;

  " | gzip > ${db_dump_dev_file}.gz

else
  echo "The file $1 doesn't exist.  Usage migrate_prod_db.bat <prod_db_file.gz>"
fi
