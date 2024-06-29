
## Introdução

> Um pacote Laravel para fácil configuração de autenticação de API com passaporte

## Instalação

Para obter a versão mais recente do Laravel Api Auth, basta solicitá-lo

```bater
compositor requer iamnotstatic/laravel-api-auth
```

## Configuração

Você pode publicar o arquivo de configuração usando este comando:

```bater
fornecedor de artesão php:publicar --provider="Iamnotstatic\LaravelAPIAuth\LaravelAPIAuthServiceProvider"
```

Migre seu banco de dados após instalar o pacote

```bater
php artesão migrar
```

Este comando criará as chaves de criptografia necessárias para gerar tokens de acesso seguro. Além disso, o comando criará clientes de “acesso pessoal” e “concessão de senha” que serão utilizados para gerar tokens de acesso

```bater
Passaporte de artesão php:instalar
```

Em seguida, você deve chamar o método Passport::routes dentro do método boot do seu AuthServiceProvider. Este método registrará as rotas necessárias para emitir tokens de acesso e revogar tokens de acesso, clientes e tokens de acesso pessoais:

```php
<?php

namespace App\Provedores;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider como ServiceProvider;
use Iluminar\Suporte\Facades\Gate;
usar Laravel\Passport\Passport;

classe AuthServiceProvider estende ServiceProvider
{
    /**
     * Os mapeamentos de políticas para o aplicativo.
     *
     * @var matriz
     */
    protegido $ políticas = [
        'App\Model' => 'App\Políticas\ModelPolicy',
    ];

    /**
     * Registre quaisquer serviços de autenticação/autorização.
     *
     * @return nulo
     */
    inicialização de função pública()
    {
        $this->registerPolicies();

        Passaporte::rotas();
    }
}
```

Em seu arquivo de configuração config/auth.php, você deve definir a opção de driver do protetor de autenticação da API como passaporte. Isso instruirá seu aplicativo a usar o TokenGuard do Passport ao autenticar solicitações de API recebidas:

```php
'guardas' => [
    'web' => [
        'driver' => 'sessão',
        'provedor' => 'usuários',
    ],

    'api' => [
        'motorista' => 'passaporte',
        'provedor' => 'usuários',
    ],
],
```

Em seu arquivo de configuração config/auth.php, você deve definir a opção model do modelo de pacote. Isso fornecerá alguns métodos auxiliares para permitir que você inspecione o token e os escopos do usuário autenticado:

```php
'provedores' => [
        'usuários' => [
            'motorista' => 'eloquente',
            'modelo' => Iamnotstatic\LaravelAPIAuth\Modelos\Usuário::classe,
        ],

        // 'usuários' => [
        // 'driver' => 'banco de dados',
        // 'tabela' => 'usuários',
        //],
    ],
```

## Uso

Agora, podemos fazer um teste simples usando ferramentas de cliente REST (Postman). Então eu testei e você pode ver as capturas de tela abaixo.

> Nesta API você deve definir dois cabeçalhos conforme listado abaixo:

```bater
Aceitar: aplicativo/json
```

![imgs do pacote](https://user-images.githubusercontent.com/46509072/78991850-d1b2ee80-7b31-11ea-8c90-e588fe7789ab.png)

Registro

![registre-se](https://user-images.githubusercontent.com/46509072/78993042-bc8b8f00-7b34-11ea-8eb4-449c7f82fd3f.png)

Conecte-se

![login](https://user-images.githubusercontent.com/46509072/78993086-d3ca7c80-7b34-11ea-807f-8bbf7baa00e8.png)

Sair

![sair](https://user-images.githubusercontent.com/46509072/78993159-f8beef80-7b34-11ea-9f0e-bff2dda268d8.png)

Obter usuário

![obterusuário](https://user-images.githubusercontent.com/46509072/78993121-e93fa680-7b34-11ea-98da-6ff837f52b78.png)

Palavra-chave esquecida

![senha esquecida](https://user-images.githubusercontent.com/46509072/78993185-0d02ec80-7b35-11ea-888b-d1ff379170c8.png)

Redefinir senha

![redefinição de senha](https://user-images.githubusercontent.com/46509072/78993214-1e4bf900-7b35-11ea-9c2b-346e1f555b97.png)

## Contribuindo

Sinta-se à vontade para fazer um fork deste pacote e contribuir enviando uma solicitação pull para aprimorar as funcionalidades.

## Como posso agradecer?

Por que não dar uma estrela no repositório do github? Eu adoraria a atenção! Por que não compartilhar o link para este repositório no Twitter ou no HackerNews? Espalhe a palavra!

Não se esqueça de [me seguir no twitter](https://twitter.com/iamnotstatic)!

Obrigado!
Abdulfatai Suleiman.

## Licença

A licença MIT (MIT). Consulte [Arquivo de licença](LICENSE.md) para obter mais informações.
