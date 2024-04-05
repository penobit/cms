<?php

namespace App;

use Smarty\Smarty;

/**
 * Template Class.
 *
 * This class wraps the Smarty template engine and provides
 * a convenient API for assigning variables to the template
 * and rendering templates.
 *
 * @link https://www.smarty.net/docs/en/
 */
class Template {
    private Smarty $engine;

    /**
     * Constructor for the PHP function.
     */
    public function __construct(public ?string $template, mixed $data = []) {
        $this->engine = new Smarty();

        if (!empty($data)) {
            $this->engine->assign($data);
        }

        $this->init();
    }

    /**
     * Magic method to return the rendered template.
     *
     * @return string the rendered template
     */
    public function __toString() {
        return $this->render() ?? '';
    }

    public function autoEscapeHtml(bool $escape) {
        $this->engine->setEscapeHtml($escape);
    }

    /**
     * Initializes the function by setting template, compile, configuration, and cache directories.
     */
    public function init() {
        $this->engine
            ->setTemplateDir(CMS_TEMPLATE_PATH)
            ->addTemplateDir(HOME.'/content/themes/')
            ->setCompileDir(TEMPLATE_CACHE_PATH)
            ->setConfigDir(CMS_TEMPLATE_PATH.'/config')
            ->setCacheDir(TEMPLATE_CACHE_PATH)
            ->setEscapeHtml(true)
        ;
    }

    /**
     * Add a template directory to the list of template directories.
     *
     * @param string $dir the directory to add
     */
    public function addTemplateDir(string $dir) {
        $this->engine->addTemplateDir($dir);
    }

    /**
     * Add a configuration directory to the list of configuration directories.
     *
     * @param string $dir the directory to add
     */
    public function addConfigDir(string $dir) {
        $this->engine->addConfigDir($dir);
    }

    /**
     * Get the Smarty engine instance.
     *
     * @return Smarty the Smarty engine instance
     */
    public function getEngine() {
        return $this->engine;
    }

    /**
     * Assign a variable to the template engine.
     *
     * @param mixed $name the name of the variable to assign
     * @param mixed $value the value to assign (default null)
     * @param null|bool $noCache whether or not to cache the value (default null)
     * @param mixed $scope the scope of the variable
     *
     * @return $this
     */
    public function with(mixed $name, mixed $value = null, ?bool $noCache, mixed $scope) {
        $this->engine->assign($name, $value, $noCache, $scope);

        return $this;
    }

    /**
     * Alias for with() method.
     *
     * @link with()
     */
    public function assign(mixed $name, mixed $value = null, ?bool $noCache, mixed $scope) {
        return $this->with($name, $value, $noCache, $scope);
    }

    /**
     * Get the template being used.
     *
     * @return null|string the template being used, or null if not set
     */
    public function getTemplate(): ?string {
        return $this->template;
    }

    /**
     * Set the template to be used.
     *
     * @param string $template the template to be used
     *
     * @return $this
     */
    public function setTemplate(string $template) {
        $this->template = $template;

        return $this;
    }

    /**
     * Render the template.
     *
     * @param null|string $template the template to render (default null)
     * @param null|array $data an array of data to assign to the template (default null)
     *
     * @return string the rendered template
     *
     * @throws \Exception if no template is set
     */
    public function render(string $template = null, ?array $data = null) {
        // Assign data to the template engine
        if (!empty($data)) {
            $this->engine->assign($data);
        }

        // Set the template to render
        $template = $template ?: $this->getTemplate();

        // Throw an exception if no template is set
        if (!$template) {
            throw new \Exception('Template is not set.');
        }

        // Render the template and return the result
        return $this->engine->display($template);
    }
}