<?php

namespace App\Views;
use App\system\Utils\Session;

/**
 * Classe responsável pelo gerenciamento das views
 * @author Renan Salustiano <renansalustiano2020@gmail.com>
 * 23/02/2022
 */
class View
{
    /**
     * Váriavel responsável por guardar os arquivos css setados
     * @param array $css
     */
    private $css;
    /**
     * Váriavel responsável por guardar os arquivos javascript setados
     * @param array $js
     */
    private $js;
    /**
     * Váriavel responsável por guardar as variáveis da view que será chamada
     * @param array $vars
     */
    private $vars;
    /**
     * Variável responsável por guardar o template que será chamado caso haja um
     * @param string $template
     */
    private $template;
    /**
     * Váriavel responsável por guardar a página que será chamada
     * @param string $page
     */
    private $page;

    private $messages;

    private $jsVars = [];

    /**
     * Caminhos das pastas Templates/Pages
     */
    const TEMPLATES_PATH = SITE_ROOT . "/App/Views/Templates/";
    const PAGES_PATH = SITE_ROOT . "/App/Views/Pages/";

    /**
     * @param bool $applyDefaults aplicar configurações padrão?
     */
    public function __construct($applyDefaults = true)
    {
        $this->vars['_MEDIA_URL'] = MEDIA_URL ?? "/";
        if ($applyDefaults) {
            $this->setTemplate('default_template');
            $this->setCssFile('estilos.css');
            $this->setJsFile('main.js');
            $this->setJsVar('const', 'SITE_URL', SITE_URL);
        }

        /**
         * mensagens vindas da última request
         */
        $this->messages = Session::getFlashData('message');
        $this->vars['logged'] = Session::verifySession();
        $this->vars['_USER'] = Session::getSession('_USER');
    }

    /**
     * setar template a ser chamado (precisa estar dentro de App/Views/Templates)
     * @param $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * Adiciona arquivo css á instancia da view
     * @param $cssFile
     * @return false|void
     */
    public function setCssFile($cssFile): void
    {
        if (is_array($cssFile)) {
            $this->css = array_merge($this->css, $cssFile);
            return;
        }
        $this->css[] = $cssFile;
    }

    /**
     * Renderiza scripts de chamada dos arquivos css setados na instancia da view
     * @return void
     */
    private function renderCssFiles(): void
    {
        if (empty($this->css)) return;

        $html = "<!-- Arquivos css renderizados dinâmicamente -->" . PHP_EOL;
        foreach ($this->css as $css) {
            if (file_exists(SITE_ROOT . "/public/css/{$css}")) {
                $href = SITE_URL . "/public/css/{$css}";
                $html .= "<link rel='stylesheet' type='text/css' href='{$href}?CACHE_BUSTING=" . md5(time()) . "'>" . PHP_EOL;
            }
        }
        $html .= "<!-- Arquivos css renderizados dinâmicamente -->" . PHP_EOL;
        echo $html;
    }

    /**
     * Adiciona arquivo javascript a instancia da view
     * @param $jsFile
     * @return void
     */
    public function setJsFile($jsFile, $options = []): void
    {
        if (is_array($jsFile)) {
            $this->js = array_merge($this->js, $jsFile);
        }
        $this->js[] = $jsFile;
    }

    /**
     * Renderiza scripts de chamada dos arquivos js setados na instancia da view
     * @return void
     */
    private function renderJsFiles(): void
    {
        if (empty($this->js)) return;

        $html = "<!-- Arquivos js renderizados dinâmicamente -->" . PHP_EOL;
        foreach ($this->js as $js) {
            if (file_exists(SITE_ROOT . "/public/js/{$js}")) {
                $src = SITE_URL . "/public/js/{$js}";
                $html .= "<script type='text/javascript' src='{$src}?CACHE_BUSTING=" . md5(time()) . "'></script>" . PHP_EOL;
            }
        }
        $html .= "<!-- Arquivos js renderizados dinâmicamente -->" . PHP_EOL;

        echo $html;
    }

    /**
     * Echo no  valor da variável caso exista senão retorna 'null'
     * @param string $varName
     */
    public function showOrNull(string $varName, $echo = true)
    {
        $indexes = explode('.',$varName);

        if(!empty($indexes)){
            $value = null;
            foreach ($indexes as $index){

                if($value === null){
                    $value  = $this->vars[$index] ?? [];
                }else{
                    $value = $value[$index] ?? [];
                }

                if(!empty($value)){
                    continue;
                }
                $value = null;
                break;
            }
            if($echo){
                echo $value;
            }else{
                return $value;
            }
        }
    }

    /**
     * Renderiza a página/template
     * @param $view
     * @param $vars
     */
    public function render(string $page, array $vars = array()): void
    {
        $this->vars = array_merge($this->vars, $vars);
        if (!empty($this->template)) {
            $path = self::TEMPLATES_PATH . $this->template . '.php';
            $this->page = $page;
        } else {
            $path = self::PAGES_PATH . $this->page . ".php";
        }

        if (file_exists($path)) {
            extract($this->vars);
            require_once $path;
            echo PHP_EOL;
        }
    }

    /**
     * Renderiza a página
     */
    private function renderPage()
    {
        $path = self::PAGES_PATH . $this->page . ".php";
        if (file_exists($path)) {
            extract($this->vars);
            require_once $path;
            echo PHP_EOL;
        }
    }

    /**
     *
     * @param $route
     * @return string
     */
    private function siteUrl($route){
        echo  SITE_URL.$route;
    }

    /**
     * @param $varName
     * @return string|null
     */
    private function getType($varName){
        return isset($this->vars[$varName]) ? gettype($this->vars[$varName]) : null;
    }


    private function renderMessages() : void{
        if(!empty($this->messages)){
            foreach ($this->messages as $type => $arrMessages){
                foreach ($arrMessages as $message_string){
                    echo "<div class='message {$type}'>{$message_string}</div>".PHP_EOL;
                }
            }
        }
    }

    /**
     * Variaveis que serão utilizadas no javascript
     * @param $type
     * @param $name
     * @param $value
     * @return false|void
     */
    public function setJsVar($type, $name, $value){
        $patternName = '/[a-zA-Z0-9_-]/';
        if(!in_array($type, ['let', 'const', 'var']) || !preg_match($patternName, $name)){
            return false;
        }

        $this->jsVars[$name] = array(
            'type' => $type,
            'value' => $value
        );
    }

    /**
     * Renderiza variáveis criadas
     */
    private function renderJsVars(){
        $script = "<script type='text/javascript'>";
        foreach ($this->jsVars as $name => $var){
            $value = $var['value'];
            switch ($value){
                case is_numeric($value):
                    $value = intval($var['value']).";";
                    break;
                case is_array($value):
                    $value = json_encode($value, JSON_UNESCAPED_UNICODE);
                    break;
                default:
                    $value = "'".$var['value']."';";
                    break;
            }
            $script .= PHP_EOL.$var['type']." ". $name." = ". $value;
        }
        $script .= PHP_EOL."</script>";
        echo $script;
    }
}