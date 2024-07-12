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

/**
 * Class ModelBackendController
 * @package Plugin\Landswitcher
 */
class ModelBackendController extends GenericModelController
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
}
