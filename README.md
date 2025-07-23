
# install all the required packages
```
composer install
```
# configure your laravel installation
```
cp .env.example .env
php artisan key:generate
```
` modify your .env file to match the environment`

` create your database and set up .env with database connection info`

# create tables and import first data
```
php artisan migrate:fresh --seed
```
# to udpate the vocabularies from the external source you can run
```
php artisan db:seed --class=UpdateVocabularySeeder
```
# link the public storage
```
php artisan storage:link
```
# add to cron something like
```
0 0 * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```
# configure your elastic index

Change the username/password and the host/port in the commands below


```
# You may need (especially in local environments) to delete the index first
# curl -X DELETE http://elastic:password@localhost:9200/erihs_services

curl -X PUT  http://elastic:password@localhost:9200/erihs_services
curl -X POST http://elastic:password@localhost:9200/erihs_services/_close
curl -X PUT -H "Content-Type: application/json" http://elastic:password@localhost:9200/erihs_services/_settings -T elasticsearch_index_settings.json
curl -X PUT -H "Content-Type: application/json" http://elastic:password@localhost:9200/erihs_services/_mappings -T elasticsearch_index_mappings.json
curl -X POST http://elastic:password@localhost:9200/erihs_services/_open
```

# In development, you can use vite autobuild
```
npm install && npm run dev
```
# You can manually build vite - it must be done in production as well
```
npm install && npm run build 
```
(you can run the build.sh script in the build_scripts directory to run it into a container)

# Cache blade icons (to be ran in production)
```
php artisan icons:cache 
```

## UTILS

# To dump roles and permissions into seeders

```
php artisan iseed users,permissions,roles,role_has_permissions,model_has_permissions,model_has_roles,scientific_disciplines,mail_templates --exclude=created_at,updated_at,full_name --force
```

## ElasticSearch

# configure .env

in the .env file you should configure the following variables:
```
ELASTIC_SCHEME=https
ELASTIC_USER=elastic
ELASTIC_PASS=asdfer123
ELASTIC_HOSTNAME=host.docker.internal:9200
```
the ELASTIC_HOST var makes use of the other ones

# reindex elastic
This will reindex all services:

```
 php artisan scout:import \\App\\Models\\Service
```


## QUEUES

we need to run laravel queues to index things in elasticsearch and to execute some other tasks.

run:
```
php artisan queue:work
```
or configure it in some other way (usually a different docker container with php artisan queue:work as its command)

