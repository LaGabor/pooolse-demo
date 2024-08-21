# pooolse-demo

1. **Change the DATABASE_URL attribute in the .env file**  
   Update the database connection string in the `.env` file to match your database configuration.

2. **Run the `composer install` command in the terminal**  
   Install all project dependencies listed in `composer.json`.

3. **Run the `php bin/console doctrine:database:create` command in the terminal**  
   Create the database specified in your `DATABASE_URL`.

4. **Run the `php bin/console doctrine:migrations:migrate` command in the terminal**  
   Apply all database migrations to update the schema.

5. **Run the `php bin/console doctrine:fixtures:load` command in the terminal**  
   Load sample data (fixtures) into the database.

6. **Run the `symfony server:start -d` command in the terminal**  
   Start the Symfony local web server in the background.
