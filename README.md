
## Introdução

> Um pacote Laravel para fácil configuração de autenticação de API com passaporte

## Instalação

```bater
compositor install
```

## Configuração

Configure o .env

Migre seu banco de dados após instalar o pacote

```bater
php artesão migrar
```
## Como a requisição deve ser

```header
Authorization: Bearer <Token retornando no login>
Accept: application/json
```

## Caminhos para login
    /api/register 
    /api/v1/auth/token
