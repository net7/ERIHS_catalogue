<?php

namespace App\Console\Commands;

use App\Models\Method;
use App\Models\MethodServiceTool;
use App\Models\Organization;
use App\Models\Tool;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SendDataToCordra extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-data-to-cordra';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $usersToSync = self::getQuery('App\Models\User');


        $this->line('Sending users');
        foreach ($usersToSync as $user) {
            $id = $user->entity_id;
            $entity = User::find($id);
            if ($entity) {
                $entity->saveDataToCordra();
            }
        }
        $this->line('Users ended');

        $this->line('Sending organizations');
        $organizationsToSync = self::getQuery('App\Models\Organization');

        foreach ($organizationsToSync as $organization) {
            $id = $organization->entity_id;
            $entity = Organization::find($id);
            if ($entity) {
                $entity->saveDataToCordra();
            }
        }
        $this->line('Organizations ended');

        $toolsToSync = self::getQuery('App\Models\Tool');

        $this->line('Sending tools');
        foreach ($toolsToSync as $tool) {
            $id = $tool->entity_id;
            $entity = Tool::find($id);
            $methodServiceTool = MethodServiceTool::where('tool_id', $id)->first();
            if ($entity && $methodServiceTool) {
                $entity->saveDataToCordra();
            }
        }
        $this->line('Tools ended');

        $methodsToSync = self::getQuery('App\Models\Method');

        $this->line('Sending methods');
        foreach ($methodsToSync as $method) {
            $id = $method->entity_id;
            $entity = Method::find($id);
            $methodServiceTool = MethodServiceTool::where('method_id', $id)->first();
            if ($entity && $methodServiceTool) {
                $entity->saveDataToCordra();
            }
        }
        $this->line('Methods ended');

        $servicesToSync = self::getQuery('App\Models\Service');

        $this->line('Sending services');
        foreach ($servicesToSync as $service) {
            $id = $service->entity_id;
            $entity = Method::find($id);
            if ($entity) {
                $entity->saveDataToCordra();
            }
        }
        $this->line('Services ended');
    }

    public static function getQuery($model)
    {
        return DB::table('cordra_entity_synchronization')
            ->where('synchronized', '=', false)
            ->where('entity_type', '=', $model)
            ->get();
    }
}
