SF		= ./bin/console --no-interaction

all:
	@echo "Please choose a task."

install:
	composer install
	${SF} doctrine:database:drop --force --if-exists
	${SF} doctrine:database:create
	${SF} doctrine:schema:drop --force
	${SF} doctrine:query:sql "DROP TABLE IF EXISTS migration_versions"
	${SF} doctrine:migrations:migrate
	${SF} doctrine:fixtures:load
