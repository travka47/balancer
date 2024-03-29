### Установка

```bash
cp .env .env.local
docker compose up -d --build
docker exec -it balancer-php bash

symfony console doctrine:migrations:migrate

symfony server:start
```

_Доступ по адресу: http://127.0.0.1:8000/api/doc_

### Последующий запуск

```bash
docker compose up -d
docker exec -it balancer-php bash

symfony server:start
```

### Тесты

```bash
docker exec -it balancer-php bash

symfony console --env=test doctrine:database:create
symfony console --env=test doctrine:schema:create

php bin/phpunit
```

### Доступ к контейнерам

```bash
docker exec -it balancer-php bash
docker exec -it balancer-mysql bash
```

### Балансировка

Первоначально процессы сортируются по двум полям от более затратных к менее затратным.
Далее предпринимаются попытки их разместить.

Выбор рабочей машины для размещения процесса основан на приоритете, который определяется как отношение свободных
ресурсов при учёте вероятного размещения процесса к общим ресурсам на рабочей станции.

Если все процеесы размещены, изменения сохраняются в базе данных.
Если хоть один процесс не размещён, процессы сортируются снова, но уже только по
оперативной памяти (затем только по процессору). Если после трёх итераций процессы разместить не вышло, сохраняется
изначальное распределение процессов.