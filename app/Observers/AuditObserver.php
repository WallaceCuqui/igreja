<?php

namespace App\Observers;

use App\Models\Audit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Arr;

class AuditObserver
{
    protected function shouldSkip($model): bool
    {
        $excludedModels = config('audit.excluded_models', []);
        $excludedTables = config('audit.excluded_tables', []);

        if (in_array(get_class($model), $excludedModels, true)) return true;
        if (in_array($model->getTable(), $excludedTables, true)) return true;

        return false;
    }

    protected function makeAudit($action, $model, $before = null, $after = null)
    {
        if ($this->shouldSkip($model)) return;

        $payload = [
            'action' => $action,
            'model_type' => get_class($model),
            'model_id' => $model->getKey(),
            'user_id' => Auth::id(),
            'ip' => Request::ip(),
            'url' => Request::fullUrl(),
            'user_agent' => Request::header('User-Agent'),
            'before' => $before ? Arr::except($before, ['updated_at','created_at']) : null,
            'after' => $after ? Arr::except($after, ['updated_at','created_at']) : null,
            'changes' => null,
        ];

        if ($before && $after) {
            $changes = [];
            foreach ($after as $k => $v) {
                $prev = $before[$k] ?? null;
                if ($prev !== $v) {
                    $changes[$k] = ['old' => $prev, 'new' => $v];
                }
            }
            $payload['changes'] = $changes ?: null;
        }

        if (config('audit.use_queue', false)) {
            dispatch(function () use ($payload) {
                Audit::create($payload);
            })->onQueue(config('audit.queue_name', 'default'));
        } else {
            Audit::create($payload);
        }
    }

    public function created($model) { $this->makeAudit('created', $model, null, $model->getAttributes()); }
    public function updated($model) { $this->makeAudit('updated', $model->getOriginal(), $model->getAttributes()); }
    public function deleted($model) { $this->makeAudit('deleted', $model->getOriginal(), null); }
}