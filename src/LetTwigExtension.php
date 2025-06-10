<?php

namespace mattesmohr\TwigLet;

class LetTwigExtension extends \Twig\Extension\AbstractExtension {

    public function getTokenParsers() {
        return [new LetTokenParser()];
    }

    public function getName() {
        return 'let';
    }
}