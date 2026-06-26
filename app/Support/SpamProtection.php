<?php

namespace App\Support;

use App\Rules\Turnstile;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SpamProtection
{
    public const MIN_SECONDS = 3;

    public const MAX_SECONDS = 7200;

    public static function turnstileEnabled(): bool
    {
        return filled(config('services.turnstile.site_key'))
            && filled(config('services.turnstile.secret_key'));
    }

    public static function siteKey(): ?string
    {
        $key = config('services.turnstile.site_key');

        return filled($key) ? $key : null;
    }

    /** @return array<string, mixed> */
    public static function rules(): array
    {
        $rules = [
            '_hp_website' => ['prohibited'],
            '_form_ts' => ['required', 'integer'],
        ];

        if (self::turnstileEnabled()) {
            $rules['cf-turnstile-response'] = ['required', 'string', new Turnstile];
        }

        return $rules;
    }

    /** @return array<string, string> */
    public static function messages(): array
    {
        return [
            '_hp_website.prohibited' => self::failureMessage(),
            '_form_ts.required' => self::failureMessage(),
            'cf-turnstile-response.required' => 'Please complete the security check.',
        ];
    }

    public static function failureMessage(): string
    {
        return 'Unable to submit right now. Please wait a few seconds and try again.';
    }

    public static function validateTiming(int $timestamp): bool
    {
        $elapsed = time() - $timestamp;

        return $elapsed >= self::MIN_SECONDS && $elapsed <= self::MAX_SECONDS;
    }

    /**
     * @return array<string, int|string>
     */
    public static function testFields(int $secondsAgo = 5): array
    {
        return [
            '_hp_website' => '',
            '_form_ts' => time() - $secondsAgo,
        ];
    }

    public static function validateRequest(Request $request, ?string $redirectTo = null): void
    {
        try {
            $request->validate(self::rules(), self::messages());
        } catch (ValidationException $e) {
            throw $redirectTo ? $e->redirectTo($redirectTo) : $e;
        }

        if (! self::validateTiming((int) $request->input('_form_ts'))) {
            $exception = ValidationException::withMessages([
                'submission' => self::failureMessage(),
            ]);

            throw $redirectTo ? $exception->redirectTo($redirectTo) : $exception;
        }
    }
}
