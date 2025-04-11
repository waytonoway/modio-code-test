<?php
namespace App\Traits;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

trait WithContextTrait
{
    protected array $context = [];

    public function withContext(array|string|null $view): static
    {
        if (is_string($view)) {
            $view = explode(',', $view);
        }

        $this->context = is_array($view) ? $view : [];

        return $this;
    }

    public function applyContext(Request $request, string $context = 'default'): void
    {
        self::withContext($request->input('context', $context));
    }
}
