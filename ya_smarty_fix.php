<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Module created in order to fix automaticaly the following vulnerability
 * https://build.prestashop.com/news/major-security-vulnerability-on-prestashop-websites/
 */
class ya_smarty_fix extends Module
{
    const FIX_FILE = '/config/smarty.config.inc.php';
    public $name;
    public $tab;
    public $version;
    public $author;
    public $need_instance;
    public $bootstrap;
    public $displayName;
    public $description;
    public $confirmUninstall;
    public $ps_versions_compliancy;

    public function __construct()
    {
        $this->name = 'ya_smarty_fix';
        $this->tab = 'advertising_marketing';
        $this->version = '1.0.0';
        $this->author = 'Yateo';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Smarty Fix');
        $this->description = $this->l('Fix Major Security Vulnerability On PrestaShop.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall this module?');
        $this->ps_versions_compliancy = ['min' => '1.6', 'max' => _PS_VERSION_];
    }

    public function getContent()
    {
        if (false === $this->hasGoodFilePermissions()) {
            throw new Exception('File ' . self::FIX_FILE . ' must be readable and writable to get the module working.' , 403);
        }

        $this->postForm();

        $this->context->smarty->assign([
            'moduleUrl' => $this->context->link->getAdminLink('AdminModules', true) . '&configure=' . $this->name,
            'fileRead' => nl2br(
                file_get_contents(_PS_CORE_DIR_ . self::FIX_FILE)
            ),
        ]);

        return $this->display(
            $this->getLocalPath() . $this->name,
            '/views/templates/hook/getContent.tpl'
        );
    }

    /**
     * Treat post form
     *
     * @return void
     */
    private function postForm()
    {
        // Fix self::FIX_FILE
        if ('1' === Tools::getValue('fix_it')) {
            $removedLines = $this->fixPrestaShop();

            if (!empty($removedLines)) {
                $this->context->smarty->assign([
                    'message' => true,
                    'message_deleted' => $this->l('Lines ' . implode(', ', $removedLines) . ' have been removed from ' . self::FIX_FILE),
                ]);
            } else {
                $this->context->smarty->assign([
                    'message' => true,
                    'message_nothing' => $this->l('There\'s nothing to fix.'),
                ]);
            }
        }

        // Uninstall module and redirect merchant to modules list
        if ('1' === Tools::getValue('delete')) {
            parent::uninstall();
            Tools::redirect($this->context->link->getAdminLink('AdminModules'));
        }
    }

    /**
     * Fix PrestaShop's vulnerability by removing following lines
     *
     * if (Configuration::get('PS_SMARTY_CACHING_TYPE') == 'mysql') {
     *     include _PS_CLASS_DIR_.'Smarty/SmartyCacheResourceMysql.php';
     *     $smarty->caching_type = 'mysql';
     *  }
     *
     * @return array
     */
    private function fixPrestaShop()
    {
        $fileToFix = _PS_CORE_DIR_ . self::FIX_FILE;
        $removeLines = [];
        $lines = file($fileToFix, FILE_IGNORE_NEW_LINES);

        // If found first element to delete, we'll delete all lines until we found "}"
        foreach($lines as $key => $line) {
            if ($line === "if (Configuration::get('PS_SMARTY_CACHING_TYPE') == 'mysql') {") {
                $removeLines[] = $key;
                continue;
            }

            if (!empty($removeLines)) {
                $removeLines[] = $key;

                if ($line === "}") {
                    break;
                }
            }
        }

        if (empty($removeLines)) {
            return $removeLines;
        }

        foreach ($removeLines as $line) {
            unset($lines[$line]);
        }

        $data = implode(PHP_EOL, $lines);
        file_put_contents($fileToFix, $data);

        return $removeLines;
    }

    private function hasGoodFilePermissions()
    {
        if (is_readable(_PS_CORE_DIR_ . self::FIX_FILE) && is_writable(_PS_CORE_DIR_ . self::FIX_FILE)) {
            return true;
        }

        return false;
    }
}
