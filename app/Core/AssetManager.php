<?php

namespace App\Core;

class AssetManager
{
    protected $styles = [];
    protected $scripts = [];

    /**
     * Enqueue a stylesheet.
     */
    public function enqueueStyle($handle, $src, $deps = [], $ver = false, $media = 'all')
    {
        $this->styles[$handle] = [
            'src' => $src,
            'deps' => $deps,
            'ver' => $ver,
            'media' => $media,
        ];
    }

    /**
     * Enqueue a script.
     */
    public function enqueueScript($handle, $src, $deps = [], $ver = false, $inFooter = false)
    {
        $this->scripts[$handle] = [
            'src' => $src,
            'deps' => $deps,
            'ver' => $ver,
            'in_footer' => $inFooter,
        ];
    }

    /**
     * Get enqueued styles.
     */
    public function getStyles()
    {
        return $this->styles;
    }

    /**
     * Get enqueued scripts.
     */
    public function getScripts($footer = false)
    {
        return array_filter($this->scripts, function($script) use ($footer) {
            return $script['in_footer'] === $footer;
        });
    }

    /**
     * Render styles.
     */
    public function renderStyles()
    {
        foreach ($this->styles as $handle => $style) {
            $src = $style['src'] . ($style['ver'] ? "?v={$style['ver']}" : "");
            echo "<link rel='stylesheet' id='{$handle}' href='{$src}' type='text/css' media='{$style['media']}' />\n";
        }
    }

    /**
     * Render scripts.
     */
    public function renderScripts($footer = false)
    {
        foreach ($this->getScripts($footer) as $handle => $script) {
            $src = $script['src'] . ($script['ver'] ? "?v={$script['ver']}" : "");
            echo "<script id='{$handle}' src='{$src}'></script>\n";
        }
    }
}
