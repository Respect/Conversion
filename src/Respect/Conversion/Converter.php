<?php

namespace Respect\Conversion;

class Converter
{
    const BIND_TYPE = 1;
    const BIND_SELECTOR = 2;
    const BIND_OPERATOR = 3;

    public $state = 1;
    public $type = null;
    public $selector = null;
    public $operator = null;
    public $typeName = null;
    public $selectorName = null;
    public $operatorName = null;

    public static function __callStatic($name, $arguments)
    {
        $c = new static;
        return $c->__call($name, $arguments);
    }

    public function bindType($type)
    {
        $typeClass = get_class($type);
        $this->typeName = static::getComponentName($typeClass);

        if ($this->type) {
            $interfaceName = static::getComponentBindInterface($typeClass);

            if ($this->type instanceof $interfaceName)
                $type = $this->type->{'bindTo'.$this->typeName}($type);
            else 
                $type = new Types\Multi($this->type, $type);

            $this->typeName = static::getComponentName(get_class($type));
        }
        
        $this->type = $type;
    }

    public function bindSelector($selector)
    {
        $selectorClass = get_class($selector);
        $this->selectorName = static::getComponentName($selectorClass);

        if ($this->selector) {
            $interfaceName = static::getComponentBindInterface($selectorClass);

            if ($this->operator) {
                $this->operator = new Operators\Common\Common\Sequence($this->operator);
                $this->operator->operateUsing($this->type, $this->selector);
                $this->selector = new Selectors\Common\Multi;
            } elseif ($this->selector instanceof Selectors\Common\Multi)
                $this->selector->selectors[] = $selector;
            elseif ($this->selector instanceof $interfaceName)
                $selector = $this->selector->{'bindTo'.$this->selectorName}($selector);
            else
                $selector = new Selectors\Common\Multi($this->selector, $selector);

            $this->selectorName = static::getComponentName(get_class($selector));
        }

        $this->selector = $selector;
    }

    public function bindOperator($operator)
    {
        $operatorClass = get_class($operator);
        $this->operatorName = static::getComponentName($operatorClass);
        $operator->operateUsing($this->type, $this->selector);

        if ($this->operator) {
            $interfaceName = static::getComponentBindInterface($operatorClass);

            if ($this->operator instanceof Operators\Common\Common\Multi)
                $this->operator->operators[] = $operator;
            elseif ($this->operator instanceof $interfaceName)
                $operator = $this->operator->{'bindTo'.$this->operatorName}($operator);
            else
                $operator = new Operators\Common\Common\Multi($this->operator, $operator);

            $this->operatorName = static::getComponentName(get_class($operator));
        }

        $operator->operateUsing($this->type, $this->selector);
        $this->operator = $operator;
    }

    protected static function getComponentName($className)
    {
        return substr($className, (strrpos($className, '\\') ?: -1)+1);
    }

    protected static function getComponentBindInterface($className)
    {
        return $className.'BindInterface';
    }

    protected static function componentInstance($className, array $arguments=array())
    {
        $mirror = new \ReflectionClass($className);
        return $mirror->newInstanceArgs($arguments);
    }

    protected function componentMatches($name, $type)
    {
        $name = ucfirst($name);
        $operatorPrefix = __NAMESPACE__.'\\Operators\\';
        $selectorPrefix = __NAMESPACE__.'\\Selectors\\';
        $typePrefix = __NAMESPACE__.'\\Types\\';

        if ($type === static::BIND_OPERATOR 
            && $this->selectorName 
            && class_exists($class = $operatorPrefix.$this->typeName.'\\'.$this->selectorName.'\\'.$name))
            return $class;
        
        if ($type === static::BIND_SELECTOR
            && $this->typeName 
            && class_exists($class = $selectorPrefix.$this->typeName.'\\'.$name))
            return $class;
        
        if ($type === static::BIND_TYPE 
            && class_exists($class = $typePrefix.$name))
            return $class;
    }

    public function __call($name, $arguments)
    {
        if ($typeClass = $this->componentMatches($name, static::BIND_TYPE)) 
            $this->bindType(static::componentInstance($typeClass, $arguments));
        elseif ($selectorClass = $this->componentMatches($name, static::BIND_SELECTOR)) 
            $this->bindSelector(static::componentInstance($selectorClass, $arguments));
        elseif ($operatorClass = $this->componentMatches($name, static::BIND_OPERATOR)) 
            $this->bindOperator(static::componentInstance($operatorClass, $arguments));
        else 
            throw new \Exception('Could not find component '.$name);
        

        return $this;
    }

    public function describe()
    {
        return array_merge(
            array(
                array('_:'.spl_object_hash($this), 'php:Class', 'phpClass:'.get_called_class())
            ),
            $this->type     ? $this->type->describe()     : null,
            $this->selector ? $this->selector->describe() : null,
            $this->operator ? $this->operator->describe() : null
        );
    }

    public function transform($input)
    {
        if ($this->operator)
            return $this->operator->transform($input);
        else
            throw new \Exception('Operator not found');
    }
}