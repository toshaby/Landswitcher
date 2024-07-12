<?php

declare(strict_types=1);

namespace Plugin\Landswitcher;


use JTL\Plugin\Bootstrapper;
use JTL\Shop;
use JTL\Smarty\JTLSmarty;
use Laminas\Diactoros\ServerRequestFactory;
use function Functional\first;

/**
 * Class Bootstrap
 * @package Plugin\Landswitcher
 */
class Bootstrap extends Bootstrapper
{
    public function renderAdminMenuTab(string $tabName, int $menuID, JTLSmarty $smarty): string
    {
        $plugin     = $this->getPlugin();
        $smarty->assign('menuID', $menuID)
            ->assign('posted', null);

        if ($tabName === 'Redirects') {
            return $this->renderModelTab($menuID, $smarty);
        }
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
