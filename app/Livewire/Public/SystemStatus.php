<?php

namespace App\Livewire\Public;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class SystemStatus extends Component
{
    public int $counter = 0;

    public string $dbStatus = 'checking';

    public ?string $dbName = null;

    public function mount(): void
    {
        $this->checkDatabase();
    }

    public function increment(): void
    {
        $this->counter++;
    }

    public function checkDatabase(): void
    {
        try {
            DB::connection()->getPdo();
            $this->dbStatus = 'connected';
            $this->dbName = DB::connection()->getDatabaseName();
        } catch (\Throwable $e) {
            $this->dbStatus = 'error: '.$e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.public.system-status');
    }
}
