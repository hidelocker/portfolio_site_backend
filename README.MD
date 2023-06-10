# PortfolioSite - Backend

[Перейти на сайт **_akella.su_**](https://akella.su)

## Запуск

Перейдите на директорию ``www`` и выполните команду:
- ``composer install``

Перейдите на г**лавную** директорию проекта и выполните команду:
- ``docker compose up -d``

## Обзор проекта
### Зависимости
- vlucas/phpdotenv: ^5.5
- firebase/php-jwt: ^6.4
- orhanerday/open-ai: ^4.7

___
###### ENDPOINTS
# 1. Projects

- ``GET /projects/page/:id``

### Request
**Headers**
```
```
**Fileds**
```
```

### Response
```
{
  "status": true,
  "body": {
    "info": {
      "total_pages": 1,
      "current_page": 1
    },
    "projects": [
      {
        "id": 1,
        "image": "2023-05-14_09-51-26.png",
        "title": "Test",
        "body": "Test",
        "stack": "test",
        "github": "test",
        "categories": "educational",
        "createdAt": "14.05.2023"
      }
    ]
  }
}
```
- ``POST /projects``
### Request
**Headers**
```
Authorization: Bearer <token>
Content-Type: multipart/form-data
```
**Fileds**
```
image               <файл>      not null
title               <текст>     not null
body                <текст>     not null
github              <текст>     not null
stack               <текст>     not null
categories          <текст>     not null
```

### Response
```
{
  "status": true,
  "body": "Проект создан"
}
```

- ``POST (PUT) /projects/put/:id``
### Request
**Headers**
```
Authorization: Bearer <token>
Content-Type: multipart/form-data
```
**Fileds**
```
image               <файл>      null
title               <текст>     not null
body                <текст>     not null
github              <текст>     not null
stack               <текст>     not null
categories          <текст>     not null
```

### Response
```
{
  "status": true,
  "body": "Проект обновлен"
}
```

- ``DELETE /projects/:id``

### Request
**Headers**
```
Authorization: Bearer <token>
```
**Fileds**
```
```

### Response
```
{
  "status": true,
  "body": "Проект удален"
}
````
___
# 2. News

- ``GET /news/page/:id``

### Request
**Headers**
```
```
**Fileds**
```
```

### Response
```
{
  "status": true,
  "body": {
    "info": {
      "total_pages": 1,
      "current_page": 1
    },
    "news": [
      {
        "id": 1,
        "image": "2023-05-14_08-50-03.png",
        "title": "Test",
        "body": "<h1>Test</h1><i>description</i>",
        "categories": "work",
        "createdAt": "14.05.2023"
      }
    ]
  }
}
```
- ``GET /news/:id``

### Request
**Headers**
```
```
**Fileds**
```
```

### Response
```
{
  "status": true,
  "body": {
    "id": 2,
    "image": "2023-05-14_09-51-18.png",
    "title": "Test",
    "body": "<h1>Test</h1><i>description</i>",
    "categories_fk": 3,
    "createdAt": "14.05.2023"
  }
}
```
- ``POST /news``
### Request
**Headers**
```
Authorization: Bearer <token>
Content-Type: multipart/form-data
```
**Fileds**
```
image               <файл>      not null
title               <текст>     not null
body                <текст>     not null
categories          <текст>     not null
```

### Response
```
{
  "status": true,
  "body": "Новость создана"
}
```

- ``POST (PUT) /news/put/:id``
### Request
**Headers**
```
Authorization: Bearer <token>
Content-Type: multipart/form-data
```
**Fileds**
```
image               <файл>      null
title               <текст>     not null
body                <текст>     not null
categories          <текст>     not null
```

### Response
```
{
  "status": true,
  "body": "Новость обновлена"
}
```

- ``DELETE /news/:id``

### Request
**Headers**
```
Authorization: Bearer <token>
```
**Fileds**
```
```

### Response
```
{
  "status": true,
  "body": "Новость удалена!"
}
```
___
# 3. Contacts

- ``GET /contacts``

### Request
**Headers**
```
```
**Fileds**
```
```

### Response
```
{
  "status": true,
  "body": [
    {
      "id": 1,
      "title": "github",
      "name": "@hidelock",
      "link": "https://github.com/hidelock"
    }
  ]
}
```

- ``PUT /contacts/:id``
### Request
**Headers**
```
Authorization: Bearer <token>
Content-Type: application/json
```
**Fileds**
```
name                <текст>     not null
link                <текст>     not null
```

### Response
```
{
  "status": true,
  "body": "Контакт обновлен"
}
```
___
# 4. Orders

- ``POST /orders``
### Request
**Headers**
```
Content-Type: application/json
```
**Fileds**
```
name                <текст>     not null
telegram            <текст>     not null
description         <текст>     not null
element             <текст>     not null
jabber              <текст>     not null
term                <число>     not null
```

### Response
```
{
  "status": true,
  "body": "Заказ успешно отправлен!"
}
```
___
# 5. Chat

- ``POST /chat``
### Request
**Headers**
```
Content-Type: application/json
```
**Fileds**
```
content            <текст>     not null
```

### Response
```
{
  "status": true,
  "body": (текст в зависимости от заданного вопроса)
}
```
___
# 6. Categories

- ``GET /categories``

### Request
**Headers**
```
```
**Fileds**
```
```

### Response
```
{
  "status": true,
  "body": [
    {
      "id": 1,
      "name": "all"
    }
  ]
}
```
- ``POST /categories``
### Request
**Headers**
```
Authorization: Bearer <token>
Content-Type: application/json
```
**Fileds**
```
name               <текст>     not null
```

### Response
```
{
  "status": true,
  "body": "Категория создана"
}
```

- ``PUT /categories/:id``
### Request
**Headers**
```
Authorization: Bearer <token>
Content-Type: application/json
```
**Fileds**
```
name                <текст>     not null
```

### Response
```
{
  "status": true,
  "body": "Категория обновлена"
}
```

- ``DELETE /categories/:id``

### Request
**Headers**
```
Authorization: Bearer <token>
```
**Fileds**
```
```

### Response
```
{
  "status": true,
  "body": "Категория удалена"
}
````
___
# 6. Admin

- ``POST /admin/login``
### Request
**Headers**
```
Content-Type: application/json
```
**Fileds**
```
login               <текст>             not null
pass                <текст (base64)>    not null
```

### Response
```
{
  "status": true,
  "body": {
    "login": "akella",
    "token": "<token>"
  }
}
```

- ``PUT /admin/pass``
### Request
**Headers**
```
Authorization: Bearer <token>
Content-Type: application/json
```
**Fileds**
```
old_pass            <текст (base64)>    not null
new_pass            <текст>             not null
confirm_pass        <текст>             not null
```

### Response
```
{
  "status": true,
  "body": "Пароль обновлен"
}
```
___

# 7. Log file

- ``GET /logs``
### Request
**Headers**
```
Authorization: Bearer <token>
```
**Fileds**
```
```

### Response
```
{
  "status": true,
  "body": {
    "content": "<текст>",
    "size_bytes": <число>
  }
}
```

- ``POST /logs``
### Request
**Headers**
```
Authorization: Bearer <token>
```
**Fileds**
```
```

### Response
```
{
  "status": true,
  "body": "Лог файл успешно очищен!"
}
```
___

# 7. LIST IP

- ``GET /list_ip/page/:id``
### Request
**Headers**
```
Authorization: Bearer <token>
```
**Fileds**
```
```

### Response
```
{
  "status": true,
  "body": {
    "info": {
      "total_pages": "1",
      "current_page": "1"
    },
    "list_ip": [
      {
        "id": 1,
        "ip": "172.22.0.1",
        "first_visit": "14.05.2023 08:36:36",
        "last_visit": "15.05.2023 12:24:28",
        "status_blocking": "allowed"
      }
    ]
  }
}
```

- ``PUT /list_ip/:id``
### Request
**Headers**
```
Authorization: Bearer <token>
Content-Type: application/json
```
**Fileds**
```
ip                  <текст>         not null
status_blocking     <текст>         not null
```

### Response
```
{
  "status": true,
  "body": "IP обновлен"
}
```

- ``DELETE /list_ip/:id``
### Request
**Headers**
```
Authorization: Bearer <token>
```
**Fileds**
```
```

### Response
```
{
  "status": true,
  "body": "IP удален"
}
```
___

