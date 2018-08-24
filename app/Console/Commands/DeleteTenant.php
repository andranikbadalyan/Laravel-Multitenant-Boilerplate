<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Hyn\Tenancy\Contracts\Repositories\WebsiteRepository;
use Hyn\Tenancy\Models\Hostname;
use Hyn\Tenancy\Contracts\Repositories\HostnameRepository;

class DeleteTenant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:delete {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes a tenant of the provided name. Only available on the local environment e.g. php artisan tenant:delete andranik';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (!app()->isLocal()) {
            $this->error('This command is only available on the local environment.');
            return;
        }

        $name = $this->argument('name');
        $fqdn = ($name=="home"?'':$name.'.').config('app.url_base');

        $this->deleteTenant($fqdn);
    }

    private function deleteTenant($fqdn)
    {
        $hostname = Hostname::where('fqdn', $fqdn)->first();
        if(!$hostname){
            $this->error("Tenant {$fqdn} doesn't exist.");
            return;
        }
        app(HostnameRepository::class)->delete($hostname, true);
        app(WebsiteRepository::class)->delete($hostname->website, true);

        $this->info("Tenant {$fqdn} successfully deleted.");
    }
}
