<?php

declare(strict_types=1);

namespace Plugin\Landswitcher;

use JTL\Plugin\PluginInterface;
use JTL\Router\Controller\Backend\GenericModelController;
use JTL\Shop;
use JTL\Smarty\JTLSmarty;
use Plugin\Landswitcher\Models\ModelRedirect;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use JTL\Helpers\Form;
use JTL\Helpers\Request;
use JTL\Pagination\Pagination;

/**
 * Class ModelBackendController
 * @package Plugin\Landswitcher
 */
class RedirectBackendController extends GenericModelController
{
    /**
     * @var int
     */
    public int $menuID = 0;

    /**
     * @var PluginInterface
     */
    public PluginInterface $plugin;

    /**
     * @inheritdoc
     */
    public function getResponse(ServerRequestInterface $request, array $args, JTLSmarty $smarty): ResponseInterface
    {
        $this->smarty = $smarty;
        $this->route  = \str_replace(Shop::getAdminURL(), '', $this->plugin->getPaths()->getBackendURL());
        $this->smarty->assign('route', $this->route);
        $this->modelClass    = ModelRedirect::class;
        $this->adminBaseFile = \ltrim($this->route, '/');

        $response = $this->handle(__DIR__ . '/adminmenu/templates/models.tpl');
        if ($this->step === 'detail') {
            $smarty->assign('defaultTabbertab', $this->menuID);
        }

        return $response;
    }

    public function handle(string $template): ResponseInterface
    {
        $this->item = new $this->modelClass($this->db);

        $this->step = $_SESSION['step'] ?? 'overview';
        $valid      = Form::validateToken();
        $action     = Request::postVar('action') ?? Request::getVar('action');
        $itemID     = $_SESSION['modelid'] ?? Request::postInt('id', null) ?? Request::getInt('id', null);
        $continue   = (bool)($_SESSION['continue'] ?? Request::postInt('save-model-continue') === 1);
        $save       = $valid && ($continue || Request::postInt('save-model') === 1);
        $modelIDs   = Request::postVar('mid', []);
        $cancel     = Request::postInt('go-back') === 1;
        if (\count($modelIDs) === 0 && Request::postInt('id', null) > 0) {
            $modelIDs = [Request::postInt('id')];
        }
        $delete       = $valid && Request::postInt('model-delete') === 1 && \count($modelIDs) > 0;
        $disable      = $valid && Request::postInt('model-disable') === 1 && \count($modelIDs) > 0;
        $enable       = $valid && Request::postInt('model-enable') === 1 && \count($modelIDs) > 0;
        $create       = Request::postInt('model-create') === 1;
        $saveSettings = Request::postVar('a') === 'saveSettings';
        if ($cancel) {
            return $this->modelPRG();
        }
        if ($continue === false) {
            unset($_SESSION['modelid']);
        }
        if ($action === 'detail') {
            $this->step = 'detail';
        }
        if ($itemID > 0) {
            $this->item = $this->modelClass::load(['id' => $itemID], $this->db);
        }
        unset($_SESSION['step'], $_SESSION['continue']);

        $models     = $this->modelClass::loadAll($this->db, [], []);

        $arExistsRedirects = [];
        foreach ($models as $rez) $arExistsRedirects[$rez->country] = $rez->url;

        if ($save === true) {
            /* проверка входных данных */
            $errors = '';

            $url = $_POST['url'];
            if(!preg_match('/^(https\:|http\:)?\/\/[a-z0-9_\-\.]+\//', $url)) $errors .= 'Урл не соответствует формату<br>';
            if(in_array($url, $arExistsRedirects)) $errors .= 'Такой урл уже существует<br>';

            if ($errors) {
                $_SESSION['modelErrorMsg'] = $errors;
            } else
                return $this->save($itemID, $continue);
        }
        if ($delete === true) {
            return $this->update($continue, $modelIDs);
        }
        if ($saveSettings === true) {
            $this->saveSettings();
        } elseif ($disable === true) {
            $this->disable($modelIDs);
        } elseif ($enable === true) {
            $this->enable($modelIDs);
        } elseif ($create === true) {
            $this->item = new $this->modelClass($this->db);
            $this->step = 'detail';
        }
        if ($this->item !== null) {
            foreach ($this->item->getAttributes() as $attribute) {
                if (\str_contains($attribute->getDataType(), '\\')) {
                    $className   = $attribute->getDataType();
                    $this->child = new $className($this->getDB());
                }
            }
        }
        $this->setMessages();

       


        $pagination = (new Pagination($template))
            ->setItemCount($models->count())
            ->assemble();

        return $this->smarty->assign('step', $this->step)
            ->assign('item', $this->item)
            ->assign('models', $models->forPage($pagination->getPage() + 1, $pagination->getItemsPerPage()))
            ->assign('arexists', $arExistsRedirects)
            ->assign('action', $this->getAction())
            ->assign('pagination', $pagination)
            ->assign('childModel', $this->child)
            ->assign('tab', $this->tab)
            ->getResponse($template);
    }
}
