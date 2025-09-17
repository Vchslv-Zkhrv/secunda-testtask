# Тестовое задание для Secunda

Я слег с перитонитом, писал этот код из больницы


## Key features:
- [x] Работа с деревьями:
    - [x] Добавление новых элементов
    - [x] Перемещение элементов внутри дерева
    - [x] Удаление частей дерева
- [x] Работа с гео-данными:
    - [x] Поиск в прямоугольной области
    - [x] Поиск в радиусе
- [x] Swagger:
    - [x] Доступен сразу по роуту `/_swagger`
    - [x] Структуры (в том числе рекурсивные)
    - [x] Авторизация для тестирования запросов
- [x] Базовая авторизация
- [x] Базовое тестирование


## Setup

### Установка проекта:
```bash
git clone git@github.com:Vchslv-Zkhrv/secunda-testtask.git zkhrv  # Клонируем этот проект в папку zkhrv
cd zkhrv  # переходим в папку
```

### Разворачивание проекта:

Если порты, указанные в `docker-compose.yaml` (3007 и 3008), свободны - можно запуститься одной командой:
```bash
make setup
```

Если нужно изменить порты:
```bash
make env;
# Добавляем в .env NGINX_EXTERNAL_PORT и DB_EXTERNAL_PORT
make build;
make up;
make generate-app-key;
make install;
make migrate;
make seed;
make apikey;
make swagger;
make test;
```


### Первый запуск:

В ходе выполнения команда `make setup` или `make:apikey` отобразит сообщение со сгенерированным API ключом:

```
 ApiKey created: `<ключ>`
```

1. Открываем `localhost:3007/_swagger` (если вы меняли `NGINX_EXTERNAL_PORT`, подставьте его).
2. Используем этот ключ для авторизации в Swagger
3. Проверяем любой роут
