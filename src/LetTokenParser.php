<?php

namespace mattesmohr\TwigLet;

class LetTokenParser extends \Twig\TokenParser\AbstractTokenParser {

    public function parse(\Twig\Token $token): \Twig\Node\Node {

        $stream = $this->parser->getStream();

        // The variable name, which is used for the context
        $name = $stream->expect(\Twig\Token::NAME_TYPE)->getValue();

        $stream->expect(\Twig\Token::OPERATOR_TYPE, '=');

        $expression = $this->parser->getExpressionParser()->parseExpression();

        $stream->expect(\Twig\Token::BLOCK_END_TYPE);

        $body = $this->parser->subparse([$this, 'decideElseBranch']);

        $else = null;

        $continue = true;

        while ($continue) {
            switch ($stream->next()->getValue()) {
                case 'else':

                    $stream->expect(\Twig\Token::BLOCK_END_TYPE);

                    $else = $this->parser->subparse([$this, 'decideLetEnd']);

                    break;

                case 'endlet':

                    $continue = false;

                    break;

                default:
                    throw new \Twig\Error\SyntaxError(\sprintf('Unexpected end of template.'));
            }
        }

        $stream->expect(\Twig\Token::BLOCK_END_TYPE);

        return new LetNode($name, $expression, $body, $else, $token->getLine());
    }

    public function decideElseBranch(\Twig\Token $token) {
        return $token->test(['else', 'endlet']);
    }

    public function decideLetEnd(\Twig\Token $token) {
        return $token->test(['endlet']);
    }

    public function getTag() {
        return 'let';
    }
}