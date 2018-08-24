<h3>Getting Started:</h3>
<ol>
  <li>
    Database: <strong>php artisan migrate</strong>
    <ul>
        <li>Creates tables: hostnames, migrations, websites</li>
    </ul>
  </li>
  <li>
    Create new tenants: <strong>php artisan tenant:create mysite andranikvbadalyan@gmail.com</strong> this will...
    <ul>
        <li>create a database with uuid (Universally Unique Identifier)</li>
        <li>run tenant migration (located in database/migrations/tenant)</li>
        <li>run tenant seeder (database/seeds/TenantDatabaseSeeder.php)</li>
        <li>add a user with provided email</li>
        <li>send a welcome/password reset email to the user (app/Notifications/TenantCreated.php)</li>
    </ul>
  </li>
  <li>To create a "home tenant" without a sub domain simply do: <strong>php artisan tenant:create home andranikvbadalyan@gmail.com</strong></li>
</ol>