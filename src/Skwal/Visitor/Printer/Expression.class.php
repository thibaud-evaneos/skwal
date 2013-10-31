<?php
namespace Skwal\Visitor\Printer
{

    class Expression implements \Skwal\Visitor\Expression
    {
        private $printedExpression;

        private $useAliases = true;

        public function useAliases($useFlag)
        {
            $this->useAliases = $useFlag;
        }

        public function printExpression(\Skwal\Expression\AliasExpression $expression)
        {
            $this->visit($expression);

            return $this->printedExpression;
        }

        public function visit(\Skwal\Expression\AliasExpression $expression)
        {
            return $expression->acceptExpressionVisitor($this);
        }

        public function visitColumn(\Skwal\Expression\DerivedColumn $column)
        {
            $scope = $column->getTable();

            $this->printedExpression = sprintf('%s.%s AS %s', $scope->getCorrelationName(), $column->getValue(), $column->getAlias());
        }

        public function visitLiteral(\Skwal\Expression\LiteralExpression $literal)
        {
            $value = $literal->getValue();

            if (is_string($value)) {
                $value = "'$value'";
            }
            else if (is_bool($value)) {
                $value = $value ? 'TRUE' : 'FALSE';
            }
            else if ($value === null) {
                $value = 'NULL';
            }

            if (trim($literal->getAlias()) != '') {
                $this->printedExpression = sprintf('%s AS %s', $value, $literal->getAlias());
            }
            else {
                $this->printedExpression = $value;
            }
        }
    }
}