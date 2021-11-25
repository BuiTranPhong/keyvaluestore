define create_db
	@mysql --execute="CREATE USER IF NOT EXISTS 'forge'@'localhost';"
	@mysql --execute="CREATE DATABASE IF NOT EXISTS $1;"
	@mysql --execute="GRANT ALL PRIVILEGES ON *.* TO 'forge'@'localhost';"
endef

define drop_db
	@mysql --execute="DROP DATABASE IF EXISTS $1;"
	@mysql --execute="DROP USER IF EXISTS 'forge'@'localhost';"
	@mysql --execute="CREATE USER 'forge'@'localhost';"
	@mysql --execute="CREATE DATABASE $1;"
	@mysql --execute="GRANT ALL PRIVILEGES ON *.* TO 'forge'@'localhost';"
endef

create_db_testing:
	$(call create_db,key_value_store_testing)

drop_db_testing:
	$(call drop_db,key_value_store_testing)

setup_db_testing:
	make create_db_testing
	php artisan migrate --env=testing

test:
	make setup_db_testing
	vendor/bin/phpunit
