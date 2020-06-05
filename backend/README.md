#First install php
    1. docker-compose run --rm backend composer install
    2. docker-compose run --rm backend php /app/init
    3. docker-compose up -d
    4. docker-compose run --rm backend yii migrate  

#After u install php run only this
    docker-compose up -d
  

