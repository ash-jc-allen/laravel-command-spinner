<?php

namespace AshAllenDesign\CommandSpinner\Traits;

use AshAllenDesign\CommandSpinner\Classes\SpinnerType;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Spatie\Fork\Fork;
use Symfony\Component\Console\Output\ConsoleOutput;

trait HasSpinner
{
    /**
     * Run a closure and display a spinner at the same time.
     *
     * @param  callable  $closure
     * @param  string  $outputText
     * @param  array  $spinnerType
     * @return bool
     */
    public function withSpinner(callable $closure, string $outputText = '', array $spinnerType = []): mixed
    {
        $this->cacheKey();

        $results = Fork::new()
            ->before(fn(): bool => $this->startSpinner())
            ->run(
                $this->spin($outputText, $spinnerType),
                $this->runCallable($closure)
            );

        $this->cleanUp();

        return $results[1];
    }

    /**
     * Start the spinner and keep going until we can detect in the
     * state that it should stopped.
     *
     * @param  string  $outputText
     * @param  array  $spinnerType
     * @return callable
     */
    private function spin(string $outputText, array $spinnerType): callable
    {
        return function () use ($outputText, $spinnerType): void {
            $section = (new ConsoleOutput)->section();

            $frames = count($spinnerType) ? $spinnerType : SpinnerType::SNAKE_VARIANT_1;

            while ($this->isSpinning()) {
                for ($loop = 0; $loop < 3; $loop++) {
                    foreach ($frames as $frame) {
                        $section->overwrite($frame.' '.$outputText);
                        usleep(100000);
                    }
                }
            }

            $section->clear();
        };
    }

    /**
     * Run the closure that was passed in by the user. After it has
     * finished running, update the state to stop the spinner.
     *
     * @param $closure
     * @return callable
     */
    private function runCallable($closure): callable
    {
        return function () use ($closure): mixed {
            $result = $closure();

            $this->stopSpinner();

            return $result;
        };
    }

    /**
     * Start the spinner.
     *
     * @return bool
     */
    private function startSpinner(): bool
    {
        return Cache::put($this->cacheKey(), true);
    }

    /**
     * Stop the spinner.
     *
     * @return bool
     */
    private function stopSpinner(): bool
    {
        return Cache::put($this->cacheKey(), false);
    }

    /**
     * Determine whether the spinner is spinning and should continue.
     *
     * @return bool
     */
    private function isSpinning(): bool
    {
        return Cache::get($this->cacheKey());
    }

    /**
     * Build and return a cache key that can be used to fetch
     * and update the spinner's state.
     *
     * @return string
     */
    private function cacheKey(): string
    {
        static $cacheKey;

        if ($cacheKey) {
            return $cacheKey;
        }

        return $cacheKey = 'spinner_'.time().'_'.Str::random(30);
    }

    /**
     * Clean up after running the spinner and delete the key
     * from the cache.
     *
     * @return void
     */
    private function cleanUp(): void
    {
        Cache::forget($this->cacheKey());
    }
}