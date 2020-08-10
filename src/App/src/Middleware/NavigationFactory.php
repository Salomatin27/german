<?php
/**
 * @see       https://github.com/zendframework/zend-navigation for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-navigation/blob/master/LICENSE.md New BSD License
 */

namespace App\Middleware;

use Exception;
use Interop\Container\ContainerInterface;
use Laminas\Navigation\Navigation;
use Laminas\Permissions\Rbac\Rbac;

class NavigationFactory
{
    /**
     * Top-level configuration key indicating navigation configuration
     *
     * @var string
     */
    const CONFIG_KEY = 'navigation';

    /**
     * Service manager factory prefix
     *
     * @var string
     */
    const SERVICE_PREFIX = 'Laminas\\Navigation\\';

    /**
     * @var array|null
     */
    private $containerNames;

    /**
     * @param ContainerInterface $container
     * @return NavigationMiddleware
     * @throws Exception
     */
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->get('config');
        $config = $config['mezzio-authorization-rbac'];
        if (!isset($config['roles'])) {
            throw new Exception('Authorization roles are not configured');
        }
        if (!isset($config['permissions'])) {
            throw new Exception('Authorization permissions are not configured');
        }

        $rbac = new Rbac();
        $rbac->setCreateMissingRoles(true);

        // roles and parents
        foreach ($config['roles'] as $role => $parents) {
            $rbac->addRole($role, $parents);
        }

        // permissions
        foreach ($config['permissions'] as $role => $permissions) {
            foreach ($permissions as $perm) {
                $rbac->getRole($role)->addPermission($perm);
            }
        }

        $containerNames = $this->getContainerNames($container);

        $containers = [];
        foreach ($containerNames as $containerName) {
            $containers[] = $container->get($containerName);
        }

        return new NavigationMiddleware($containers, $rbac);
    }

    /**
     * Get navigation container names
     *
     * @param  ContainerInterface $container
     * @return array
     */
    private function getContainerNames(ContainerInterface $container)
    {
        if ($this->containerNames !== null) {
            return $this->containerNames;
        }

        if (! $container->has('config')) {
            $this->containerNames = [];
            return $this->containerNames;
        }

        $config = $container->get('config');
        if (! isset($config[self::CONFIG_KEY])
            || ! is_array($config[self::CONFIG_KEY])
        ) {
            $this->containerNames = [];
            return $this->containerNames;
        }

        $names = array_keys($config[self::CONFIG_KEY]);

        if (count($names) === 1) {
            $this->containerNames[] = Navigation::class;
            return $this->containerNames;
        }

        foreach ($names as $name) {
            $this->containerNames[] = self::SERVICE_PREFIX . ucfirst($name);
        }

        return $this->containerNames;
    }
}
