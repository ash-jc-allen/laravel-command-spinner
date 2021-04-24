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
     * @return bool
     */
    public function withSpinner(callable $closure, string $outputText = '', array $spinnerType = []): bool
    {
        $section = (new ConsoleOutput)->section();

        Fork::new()
            ->before(fn(): bool => $this->startSpinner())
            ->run(
                $this->spin($section, $outputText, $spinnerType),
                $this->runCallable($closure)
            );

        return true;
    }

    /**
     * Start the spinner and keep going until we can detect in the
     * state that it should stopped.
     *
     * @param $section
     * @param  string  $outputText
     * @param  array  $spinnerType
     * @return callable
     */
    private function spin($section, string $outputText, array $spinnerType): callable
    {
        return function () use ($outputText, $section, $spinnerType): void {
            $frames = count($spinnerType) ? $spinnerType : SpinnerType::SNAKE_VARIANT_1;

            while ($this->isSpinning()) {
                foreach ($frames as $frame) {
                    $section->overwrite($frame.' '.$outputText);
                    usleep(100000);
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
        return function () use ($closure): void {
            $closure();

            $this->stopSpinner();
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
     * @return bool
     */
    private function cacheKey(): bool
    {
        static $cacheKey;

        if ($cacheKey) {
            return $cacheKey;
        }

        return $cacheKey = 'spinner_'.time().'_'.Str::random(30);
    }
}