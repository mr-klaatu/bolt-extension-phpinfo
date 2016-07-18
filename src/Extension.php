<?php

namespace Bolt\Extensions\mr-klaatu\phpinfo;

use Bolt\Application;
use Bolt\Extension\SimpleExtension;
use Symfony\Component\HttpFoundation\Request;

/**
 * PhpInfo extension class.
 *
 * @author Mr-Klaatu <mr-klaatu@me.com>
 */
class PhpInfoExtension extends SimpleExtension
{

    public function getServiceProviders()
    {
        return [
            $this,
            new ControllerProvider($this->getConfig()),
        ];
    }

    protected function registerAssets()
    {
        return [

        ];
    }

    protected function registerTwigFunctions()
    {
        $app = $this->getContainer();

        return [
            'phpinfo' => [
                [$app['phpinfo.twig'], 'twigPhpInfo'],

            ]
        ];
    }

    protected function registerTwigPaths()
    {
        return ['templates'];
    }

    protected function registerMenuEntries()
    {
        return [
            (new MenuEntry('phpinfo', 'phpinfo'))
                ->setLabel(Trans::__('Display PhpInfo'))
                ->setIcon('fa:pencil-square-o')
                ->setPermission('admin||root||developer'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function registerBackendControllers()
    {
        $app = $this->getContainer();
        return [
        ];
    }

    public function initialize()
    {

    }


    /**
     * Checks that the user has a non-guest role.
     *
     * @return bool
     */
    public function checkAuth()
    {
        $currentUser = $this->app['users']->getCurrentUser();
        $currentUserId = $currentUser['id'];
        foreach (['admin', 'root', 'developer'] as $role) {
            if ($this->app['users']->hasRole($currentUserId, $role)) {
                return true;
            }
        }
        return false;
    }


    /**
     * Handles GET requests on /bolt/phpinfo and return a template
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function twigPhpInfo(Request $request)
    {

        ob_start();
        phpinfo();
        $phpinfo = ob_get_contents();
        ob_end_clean();

        $phpinfo = preg_replace( '%^.*<body>(.*)</body>.*$%ms','$1',$phpinfo);

        return $this->app['templates']->render('phpinfo.twig', array('title' => 'Phpinfo','phpinfo' => $phpinfo));
    }

}
