<?php

namespace mattesmohr\TwigLet;

class LetNode extends \Twig\Node\Node {

    public function __construct($name, \Twig\Node\Node $tests, ?\Twig\Node\Node $else, $line) {
        parent::__construct(['tests' => $tests, 'else' => $else], ['name' => $name], $line);
    }

    public function compile(\Twig\Compiler $compiler) {

        $compiler->addDebugInfo($this);

        for ($i = 0, $count = \count($this->getNode('tests')); $i < $count; $i += 2) {

            $compiler
                ->write('if (')
                ->subcompile($this->getNode('tests')->getNode((string) $i))
                ->raw(") {\n")
                ->indent()
                ->write('$context[\''.$this->getAttribute('name').'\'] = ')
                ->subcompile($this->getNode('tests')->getNode((string) $i))
                ->raw(";\n");

            // Checks if there is some body
            if ($this->getNode('tests')->hasNode((string) ($i + 1))) {

                $body = $this->getNode('tests')->getNode((string) ($i + 1));

                $compiler->subcompile($body);
            }
        }

        if ($this->hasNode('else')) {
            $compiler
                ->outdent()
                ->write("} else {\n")
                ->indent()
                ->subcompile($this->getNode('else'));
        }

        $compiler
            ->outdent()
            ->write("}\n")
            ->write('unset($context[\''.$this->getAttribute('name').'\']);')
            ->write("\n");
    }
}