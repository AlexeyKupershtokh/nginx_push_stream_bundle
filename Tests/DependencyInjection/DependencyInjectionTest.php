<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Alawar\NginxPushStreamBundle\Tests\DependencyInjection;


use Symfony\Component\DependencyInjection\Definition;

abstract class DependencyInjectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Assertion on the Class of a DIC Service Definition.
     *
     * @param Definition $definition
     * @param string $expectedClass
     */
    protected function assertDICDefinitionClass($definition, $expectedClass)
    {
        $this->assertEquals($expectedClass, $definition->getClass(),
            "Expected Class of the DIC Container Service Definition is wrong."
        );
    }

    /**
     * @param Definition $definition
     * @param array $args
     */
    protected function assertDICConstructorArguments($definition, $args)
    {
        $this->assertEquals(
            $args,
            $definition->getArguments(),
            "Expected and actual DIC Service constructor arguments of definition '"
            .$definition->getClass()."' don't match."
        );
    }

    /**
     * @param int $pos
     * @param Definition $definition
     * @param string $methodName
     * @param array $params
     */
    protected function assertDICDefinitionMethodCallAt($pos, $definition, $methodName, array $params = null)
    {
        $calls = $definition->getMethodCalls();
        if (isset($calls[$pos][0])) {
            $this->assertEquals(
                $methodName,
                $calls[$pos][0],
                "Method '".$methodName."' is expected to be called at position $pos."
            );

            if ($params !== null) {
                $this->assertEquals(
                    $params,
                    $calls[$pos][1],
                    "Expected parameters to methods '".$methodName."' do not match the actual parameters."
                );
            }
        } else {
            $this->fail("Method '".$methodName."' is expected to be called at position $pos.");
        }
    }
}
