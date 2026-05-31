# TIGR
## Инструкция по развертыванию

### Технологии

- php 8.3+
- PostgreSQL 16+
- apache
  
### Развертывание

#### Первичная установка
```bash
git clone https://github.com/Artem19140/tigr.git
cd tigr
cp .env.example .env

```
Далее необходимо заменить параметры подключения  к БД в .env
```
DB_CONNECTION=
DB_HOST=
DB_PORT=
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
```
Также нужно указать переменную 
```
APP_URL=
```
Заполнить данные для входа админа
```
SUPER_ADMIN_LOGIN=
SUPER_ADMIN_PASSWORD=
```

#### Установка зависимостей
```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
```

#### Инициализация приложения
```bash
php artisan key:generate

php artisan migrate:fresh --seed --force

php artisan storage:link 

php artisan optimize:clear 
php artisan optimize
```
#### Scheduler (cron)

Добавить в crontab:

```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1

```
#### Apache
DocumentRoot должен указывать на директорию:
```
/public
```
#### Дополнительно
Необходимо разархивировать в папку(из корня проекта)
```
storage/app/public/
```
содержимое zip [архива](https://udsucrm.bitrix24.ru/file/LKOElB4yGD65hSc4W8jA)

### Обновление проекта(вытягивание с GitHub)
```bash

cd /path-to-project

php artisan down || true



//pull или push 
//Если возможно, то только при наличии изменений
// в GitHub выполнить блок кода:
------
composer install --no-dev --optimize-autoloader

npm ci
npm run build

php artisan migrate --force 

php artisan optimize:clear 
php artisan optimize 
-----

php artisan up
```

### Обновление проекта пример
```bash

cd /path-to-project

php artisan down || true

git fetch origin main

LOCAL=$(git rev-parse HEAD)
REMOTE=$(git rev-parse origin/main)

if [ "$LOCAL" != "$REMOTE" ]; then
    git reset --hard origin/main
    composer install --no-dev --optimize-autoloader

    npm ci
    npm run build

    php artisan migrate --force 

    php artisan optimize:clear 
    php artisan optimize 
fi

php artisan up
```