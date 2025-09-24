<?php

namespace mattesmohr\TwigLet;

class LetNode extends \Twig\Node\Node {

    public function __construct($name, \Twig\Node\Node $expression, \Twig\Node\Node $body, ?\Twig\Node\Node $else, $line) {

        $nodes = ['expression' => $expression, 'body' => $body];

        if ($else !== null) {
            $nodes['else'] = $else;
        }

        parent::__construct($nodes, ['name' => $name], $line);
    }

    public function compile(\Twig\Compiler $compiler) {

        $compiler->addDebugInfo($this);

        $compiler
            ->write('if (')
            ->subcompile($this->getNode('expression'))
            ->raw(") {\n");

        // Make a copy first
        $compiler
            ->indent()
            ->write('$copy = $context;')
            ->raw(";\n");

        $compiler
            ->write('$context[\''.$this->getAttribute('name').'\'] = ')
            ->subcompile($this->getNode('expression'))
            ->raw(";\n");

        $compiler
            ->subcompile($this->getNode('body'))
            ->raw(";\n");

        // Scope the changes
        $compiler
            ->write('unset($context[\''.$this->getAttribute('name').'\']);')
            ->raw(";\n");

        // Reset the context
        $compiler
            ->write('$context = array_merge($copy, $context);')
            ->raw(";\n");

        $compiler
            ->write('unset($copy)')
            ->raw(";\n");

        if ($this->hasNode('else')) {
            $compiler
                ->write("} else {\n")
                ->indent()
                ->subcompile($this->getNode('else'));
        }

        $compiler
            ->outdent()
            ->write("}\n")
            ->write("\n");
    }
}