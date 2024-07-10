<?php

declare(strict_types=1);

namespace Plugin\landswitcher;

use JTL\Alert\Alert;
use JTL\Catalog\Category\Kategorie;
use JTL\Catalog\Product\Artikel;
use JTL\Consent\Item;
use JTL\Events\Dispatcher;
use JTL\Events\Event;
use JTL\Helpers\Form;
use JTL\Helpers\Request;
use JTL\Link\LinkInterface;
use JTL\Plugin\Bootstrapper;
use JTL\Router\Router;
use JTL\Shop;
use JTL\Shopsetting;
use JTL\Smarty\JTLSmarty;
use Laminas\Diactoros\ServerRequestFactory;
use Plugin\jtl_test\Models\ModelFoo;
use Plugin\jtl_test\Smarty\Registrator;

use function Functional\first;

/**
 * Class Bootstrap
 * @package Plugin\jtl_test
 */
class Bootstrap extends Bootstrapper
{
    public function renderAdminMenuTab(string $tabName, int $menuID, JTLSmarty $smarty): string
    {
        $plugin     = $this->getPlugin();
        $backendURL = \method_exists($plugin->getPaths(), 'getBackendURL')
            ? $plugin->getPaths()->getBackendURL()
            : Shop::getAdminURL() . '/plugin.php?kPlugin=' . $plugin->getID();

        $smarty->assign('menuID', $menuID)
            ->assign('posted', null);

        $template = 'testtab.tpl';
        if ($tabName === 'Redirects') {
            return $this->renderModelTab($menuID, $smarty);
        }

        return $smarty->assign('backendURL', $backendURL)
            ->fetch($this->getPlugin()->getPaths()->getAdminPath() . '/templates/' . $template);
    }

    private function renderModelTab(int $menuID, JTLSmarty $smarty): string
    {
        $controller         = new ModelBackendController(
            $this->getDB(),
            $this->getCache(),
            Shop::Container()->getAlertService(),
            Shop::Container()->getAdminAccount(),
            Shop::Container()->getGetText()
        );


        $controller->menuID = $menuID;
        $controller->plugin = $this->getPlugin();
        $request            = ServerRequestFactory::fromGlobals($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);
        $response           = $controller->getResponse($request, [], $smarty);

        if (\count($response->getHeader('location')) > 0) {
            \header('Location:' . first($response->getHeader('location')));
            exit();
        }

        return (string)$response->getBody();
    }
}
