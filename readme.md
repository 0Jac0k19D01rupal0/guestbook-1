# [PHP, Symfony 4] Guestbook

![GuestBook](https://gb.root7.ru/assets/img/preloader1.gif) <br>
Guestbook allows site users to leave messages on the site. All data entered by the user are stored in the MySQL database, and the database stores the data about the IP user and its information about the browser.
**Developed by Symfony 4** | [Example: GuestBook](https://gb.root7.ru)

## Requirements for project deployment

* PHP 7.2 =<
* Composer
* Web server **Apache 2**

## How to Deploy Project

1. Cloning repository: `git clone https://github.com/zomboy7/guestbook.git`
2. Rename project forder `mv guestbook <your_domain>` and enter folder `cd <you_domain>`
3. Setting **.env**:
    1. Rename .env.example: `mv .env.example .env`
    2. Configure **.env** 
    ```Apache
    # Changing the development environment to prod
    APP_ENV=dev
    # Build connecting
    DATABASE_URL=mysql://db_user:db_password@db_ip:3306/db_name
    
    # Configure Svift Mailer
    # For Gmail as a transport, use: "gmail://username:password@localhost"
    # For a generic SMTP server, use: "smtp://localhost:25?encryption=&auth_mode="
    MAILER_URL=gmail://gmail_username:gmail_password@localhost
    ```
4. Installing composer packages `composer install --no-dev --optimize-autoloader` and updating `composer update`
5. Clearing cache `php bin/console cache:clear --env=prod --no-debug`
6. Install CKEditor Bundle `php app/console ckeditor:install`
7. Install the Assets `php bin/console assets:install public`
8. Configure database:
    1. Create base `php bin/console doctrine:database:create`
    2. Make migrations `php bin/console make:migrations` and migrate `php bin/console doctrine:migrations:migrate`
9. Add permissoins `chmod 777 public/uploads/pictures`
10. Go to your web-site
#### More documentation
[Configuring Symfony](https://symfony.com/doc/current/configuration.html)<br>
[How to deploy a Symfony Application](https://symfony.com/doc/current/deployment.html)

## How load fixtures
Fixtures have already been made, you only need to load them with the command:
`php bin/console doctrine:fixtures:load`

## How add new Admin
Admin can be added using the command:
`php bin/console guestbook:admin <username>`
where `<username>` - it is username new admin