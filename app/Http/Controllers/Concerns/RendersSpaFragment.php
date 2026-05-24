<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\View\View;

trait RendersSpaFragment
{
    /**
     * Return a full Blade page or only the main content fragment when the
     * browser requests an in-app navigation (X-SPA-Partial).
     *
     * @param  array<string, mixed>  $data
     */
    protected function spaView(string $view, array $data = [], ?string $documentTitle = null): View|Response
    {
        if ($this->wantsSpaFragment()) {
            $label = $documentTitle ?? Str::headline(str_replace('-', ' ', Str::afterLast($view, '.')));

            return response(
                view($view, $data)->fragment('spa-main'),
                200,
                [
                    'X-SPA-Title' => $label,
                    'Cache-Control' => 'private, no-cache',
                ]
            );
        }

        return view($view, $data);
    }

    protected function wantsSpaFragment(): bool
    {
        return request()->ajax()
            && (string) request()->header('X-SPA-Partial', '') === '1';
    }
}
