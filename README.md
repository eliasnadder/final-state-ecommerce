# Final State E-commerce

[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](https://opensource.org/licenses/MIT)
[![Build Status](https://img.shields.io/github/actions/workflow/status/eliasnadder/final-state-ecommerce/main.yml?branch=main)]()

## Table of Contents

- [Final State E-commerce](#final-state-e-commerce)
  - [Table of Contents](#table-of-contents)
  - [Description](#description)
  - [Features](#features)
  - [Tech Stack](#tech-stack)
  - [File Structure Overview](#file-structure-overview)
  - [Prerequisites](#prerequisites)
  - [Installation](#installation)
  - [Usage](#usage)
  - [Configuration](#configuration)
  - [Contributing](#contributing)
  - [License](#license)

## Description

This project appears to be a Laravel-based e-commerce application.

## Features

Key features of this project likely include:

-   User authentication and authorization
-   Product catalog management
-   Shopping cart functionality
-   Checkout process
-   Order management

## Tech Stack

The project utilizes the following technologies:

-   PHP (Laravel Framework)
-   Blade Templating Engine
-   JavaScript
-   CSS
-   Tailwind CSS
-   Vite
-   Docker

Key dependencies include:

-   `laravel-vite-plugin`
-   `axios`
-   `concurrently`
-   `tailwindcss`
-   `vite`

## File Structure Overview

```text
.
├── app/
├── bootstrap/
├── config/
├── database/
├── public/
├── resources/
├── routes/
├── storage/
├── tests/
├── .editorconfig
├── .env.example
├── .gitattributes
├── .gitignore
├── Dockerfile
├── artisan
├── composer.json
├── composer.lock
├── package.json
├── phpunit.xml
├── render.yaml
├── vite.config.js
└── README.md
```

## Prerequisites

-   PHP 8.1 or higher
-   Composer
-   Node.js
-   NPM or Yarn

## Installation

1.  Clone the repository:

    ```bash
    git clone https://github.com/eliasnadder/final-state-ecommerce.git
    cd final-state-ecommerce
    ```

2.  Install PHP dependencies:

    ```bash
    composer install
    ```

3.  Install JavaScript dependencies:

    ```bash
    npm install # or yarn install
    ```

4.  Copy the `.env.example` file to `.env` and configure your database settings.

    ```bash
    cp .env.example .env
    ```

5.  Generate an application key:

    ```bash
    php artisan key:generate
    ```

6.  Run database migrations:

    ```bash
    php artisan migrate
    ```

## Usage

To start the development server:

```bash
npm run dev
```

To build the project for production:

```bash
npm run build
```

## Configuration

The application can be configured using environment variables. Refer to the `.env.example` file for available options.

## Contributing

Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License

This project is open-sourced software licensed under the MIT license.
