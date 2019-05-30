# Laravel Nested Sets example
## Install

```bash
cp .env.example .env
docker-compose up -d
docker-compose exec app php artisan key:generate
docker-compose exec db bash

mysql -u root -p
GRANT ALL ON laravel.* TO 'laravel'@'%' IDENTIFIED BY 'secret';
FLUSH PRIVILEGES;
EXIT;
exit

docker-compose exec app php artisan migrate
```

## Run tests
```bash
docker-compose exec app vendor/bin/phpunit
```

## Usage
POST http://127.0.0.1/api/organizations
```json
{
	"org_name": "Paradise island",
	"daughters": [
		{
			"org_name": "Banana Tree",
			"daughters": [
				{"org_name": "Yellow Banana"},
				{"org_name": "Brown Banana"},
				{"org_name": "Black Banana"}
			]
		},
		{
			"org_name": "Big Banana Tree",
			"daughters": [
				{"org_name": "Yellow Banana"},
				{"org_name": "Brown Banana"},
				{"org_name": "Green Banana"},
				{
					"org_name": "Black banana",
					"daughters": [{
						"org_name": "Phoneutria Spider"
					}]
				}
			]	
		}
	]
}
```

GET http://127.0.0.1/api/organizations/black banana

note: header Content-Type:application/json is mandatory.