<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\User;
use Illuminate\Support\Facades\Hash;
use App\Notifications\TenantCreated;
use Hyn\Tenancy\Environment;
use Hyn\Tenancy\Models\Website;
use Hyn\Tenancy\Contracts\Repositories\WebsiteRepository;
use Hyn\Tenancy\Models\Hostname;
use Hyn\Tenancy\Contracts\Repositories\HostnameRepository;


class CreateTenant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:create {name} {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a tenant with the provided name and email address e.g. php artisan tenant:create anrdanik andranikvbadalyan@gmail.com';


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = $this->argument('name');
        $email = $this->argument('email');
        $fqdn = ($name=="home"?'':$name.'.').config('app.url_base');

        if ($this->tenantExists($fqdn)) {
            $this->error("A tenant with name '{$name}' already exists.");
            return;
        }

        $hostname = $this->registerTenant($fqdn);

        app(Environment::class)->tenant($hostname->website);

        $password = str_random();
        $this->addTenant($name, $email, $password)
            ->notify(new TenantCreated($hostname));


        $this->info("Tenant '{$name}' is created and is now accessible at {$hostname->fqdn}");
    }

    private function tenantExists($fqdn)
    {
        return Hostname::where('fqdn', $fqdn)->exists();
    }

    private function registerTenant($fqdn)
    {
        $website = new Website;
        app(WebsiteRepository::class)->create($website);

        $hostname = new Hostname;
        $hostname->fqdn = $fqdn;
        app(HostnameRepository::class)->attach($hostname, $website);

        return $hostname;
    }

    private function addTenant($name, $email, $password)
    {
        $tenant = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password)
        ]);
        $tenant->guard_name = 'web';
        $tenant->assignRole('tenant');

        return $tenant;
    }
}
