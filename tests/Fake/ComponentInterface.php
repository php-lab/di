<?php
declare(strict_types=1);

/**
 * @author    Yuriy Davletshin <yuriy.davletshin@gmail.com>
 * @copyright 2015 Yuriy Davletshin
 * @license   MIT
 */
namespace PhpLab\Di\Fake;

/**
 * Fake component interface.
 */
interface ComponentInterface
{
    public function getResult(string $value);
}
