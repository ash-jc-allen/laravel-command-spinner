<?php

namespace AshAllenDesign\CommandSpinner\Traits;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Spatie\Fork\Fork;
use Symfony\Component\Console\Output\ConsoleOutput;

trait HasSpinner
{
    public $frames = ['⠏', '⠛', '⠹', '⢸', '⣰', '⣤', '⣆', '⡇'];

    /**
     * Run a closure and display a spinner at the same time.
     *
     * @param  callable  $closure
     * @param  string  $outputText
     * @return bool
     */
    public function withSpinner(callable $closure, string $outputText = ''): bool
    {
        $cacheKey = 'spinner_'.time().'_'.Str::random(30);

        $section = (new ConsoleOutput)->section();

        Fork::new()
            ->before(fn(): bool => Cache::put($cacheKey, true))
            ->run(
                $this->spin($cacheKey, $section, $outputText),
                $this->runCallable($cacheKey, $closure)
            );

        return true;
    }

    /**
     * Start the spinner and keep going until we can detect in the
     * state that it should stopped.
     *
     * @param $cacheKey
     * @param $section
     * @param  string  $outputText
     * @return callable
     */
    private function spin($cacheKey, $section, string $outputText): callable
    {
        return function () use ($outputText, $cacheKey, $section): void {
            while (Cache::get($cacheKey)) {
                foreach ($this->frames as $frame) {
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
     * @param $cacheKey
     * @param $closure
     * @return callable
     */
    private function runCallable($cacheKey, $closure): callable
    {
        return static function () use ($closure, $cacheKey): void {
            $closure();

            Cache::put($cacheKey, false);
        };
    }
}