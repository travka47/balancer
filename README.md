### Установка
```bash
cp .env .env.local
docker compose up -d --build
docker exec -it balancer-php bash

symfony server:start
```
_Доступ по адресу: http://127.0.0.1:8000_
### Последующий запуск
```
docker compose up -d
docker exec -it balancer-php bash

symfony server:start
```

### Доступ к контейнерам
```
docker exec -it balancer-php bash
docker exec -it balancer-mysql bash
```