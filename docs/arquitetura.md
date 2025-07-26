# Arquitetura do Projeto

Este documento detalha a arquitetura do projeto Fynn, como o ambiente de desenvolvimento está configurado e como utilizar os componentes da interface do usuário baseados em Volt.

1. Visão Geral da Arquitetura
O projeto é construído sobre o framework Laravel 12, utilizando Livewire 3 com a sintaxe Volt para criar componentes de front-end reativos e dinâmicos com PHP.

A arquitetura principal é composta por:

Backend: Laravel (PHP)
Frontend: Livewire (Volt) e Blade (PHP/HTML)
Estilização: Tailwind CSS
Banco de Dados: PostgreSQL
Ambiente de Desenvolvimento: Docker e Docker Compose
A combinação de Livewire e Volt permite criar componentes interativos sem escrever JavaScript, mantendo toda a lógica no backend com PHP.

2. Ambiente de Desenvolvimento com Docker
Para facilitar o desenvolvimento e garantir a consistência entre os ambientes, utilizamos Docker. A configuração de desenvolvimento (docker-compose.dev.yml) foi projetada para oferecer hot-reloading tanto para o backend (Laravel) quanto para o frontend (Vite).

Como Usar
Construir e Iniciar os Containers: No terminal, na raiz do projeto, execute:
bash
docker-compose up -d --build 
docker-compose exec laravel.test composer install
docker-compose exec laravel.test npm install
docker-compose exec laravel.test php artisan migrate
docker-compose exec laravel.test npm run dev
Este comando irá construir as imagens e iniciar os serviços app, db e nginx em modo detached (-d).
Acessar a Aplicação:
Aplicação Laravel: http://localhost:${APP_PORT}
Vite Dev Server: http://localhost:5173
Graças aos volumes do Docker, qualquer alteração nos arquivos do projeto (PHP, Blade, etc.) será refletida instantaneamente na aplicação, sem a necessidade de reconstruir os containers.

3. Componentes Volt
Volt simplifica a criação de componentes Livewire, permitindo que a lógica PHP e a view Blade residam em um único arquivo.

Criando um Componente Volt
Para criar um novo componente, utilize o comando Artisan:

bash
php artisan make:volt nome-do-componente
Isso criará um novo arquivo em resources/views/livewire/nome-do-componente.blade.php.

Estrutura de um Componente Volt:

```php
<?php

use function Livewire\Volt\{state};

// Lógica PHP aqui (propriedades, métodos, etc.)
state(['count' => 0]);

$increment = fn () => $this->count++;

?>

{{-- Template Blade aqui --}}
<div>
    <h1>Contador: {{ $count }}</h1>
    <button wire:click="increment">+</button>
</div>
```

Usando um Componente Volt
Para usar o componente em uma view ou em outro componente, utilize a tag do Livewire:

```html
<livewire:nome-do-componente />
```

4. Fluxograma das Views com main.blade.php
A estrutura de views do Volt neste projeto utiliza um layout principal que encapsula o conteúdo das páginas. O arquivo resources/views/livewire/main.blade.php atua como um layout persistente, melhorando a performance da navegação.

Aqui está um fluxograma que descreve o fluxo de renderização:

      +--------------------------------+
      |      Requisição do Usuário     |
      | (Ex: http://localhost/dashboard) |
      +--------------------------------+
                   |
                   v
      +--------------------------------+
      |        Rotas (web.php)         |
      | Volt::route('/dashboard', 'dashboard'); |
      +--------------------------------+
                   |
                   v
      +--------------------------------+
      |     Componente Volt Principal   |
      | (Ex: livewire/main.blade.php) |
      +--------------------------------+
                   |
                   v
      +--------------------------------+
      |   Renderiza o Layout Secundário|
      | (livewire/dashboard.blade.php) |
      |      <x-main full-width>       |
      +--------------------------------+
                   |
                   v
      +--------------------------------+
      | O conteúdo do componente da   |
      | rota é injetado na variável   |
      |          `$slot` do layout     |
      +--------------------------------+
                   |
                   v
      +--------------------------------+
      |    HTML Final é Enviado para   |
      |          o Navegador           |
      +--------------------------------+

Explicação do Fluxo:

Uma rota definida com Volt::route() aponta para um componente Volt (ex: dashboard).
Este componente (dashboard.blade.php) é carregado.
Dentro do componente dashboard, a tag <x-main> é usada. Isso informa ao Blade para usar o layout definido em resources/views/components/main.blade.php.
O conteúdo do componente dashboard é passado para a variável $slot dentro do main.blade.php.
O main.blade.php define a estrutura HTML principal (incluindo <html>, <head>, <body>, menus, etc.) e renderiza o conteúdo da página (o $slot) no local apropriado.
O atributo full-width é um exemplo de prop que pode ser passada para o layout para customizar sua aparência, como remover margens laterais para páginas que precisam de largura total.