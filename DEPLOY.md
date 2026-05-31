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

#### Установка зависимостей
```bash
composer install --no-dev
```

#### Инициализация приложения
```bash
php artisan key:generate

php artisan migrate --force

php artisan db:seed

php artisan storage:link 

php artisan optimize:clear 
php artisan optimize
```
#### Scheduler (cron)

Добавить в crontab:

```bash
* * * * * cd /var/www/tigr && php artisan schedule:run >> /dev/null 2>&1
```
#### Apache
DocumentRoot должен указывать на директорию:
```
/public
```
#### Дополнительно
Необходимо разархивировать в папку
```
storage/app/public/
```
содержимое zip [архива](https://udsucrm.bitrix24.ru/file/LKOElB4yGD65hSc4W8jA)

### Обновление проекта(вытягивание с GitHub)
```bash
php artisan down 

git pull 

composer install --no-dev

php artisan migrate --force 

php artisan optimize:clear 
php artisan optimize 

php artisan up
```

