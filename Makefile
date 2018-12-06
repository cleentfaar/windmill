build:
	docker-compose build windmill
	
watch: build
	docker-compose run --rm windmill
	
run:
	docker-compose up windmill

clean:
	docker-compose down --remove-orphans
