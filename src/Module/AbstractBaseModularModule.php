<?php

namespace RebelCode\Modular\Module;

use Dhii\Modular\Module\ModuleInterface;

/**
 * Base functionality for modular modules.it
 *
 * @since [*next-version*]
 */
abstract class AbstractBaseModularModule extends AbstractBaseModule
{
    /*
     * Provides modular functionality for modules.
     *
     * @since [*next-version*]
     */
    use ModularModuleTrait;

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _getModuleServiceKey(ModuleInterface $module)
    {
        return sprintf('%s_module', $module->getKey());
    }
}
