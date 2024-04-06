<?php

namespace Isobar\Megamenu\Model\Configurator\Row\Column;

class EntityFactory
{
    public function create(array $entityData = [])
    {
        $className = 'Isobar\Megamenu\Model\Configurator\Row\Column';
        $value = $entityData['value'];
        switch (substr($value, 0, 5)) {
            case ('produ'):
                $className .= '\Products';
                $value = substr($value, 9, strlen($value) - 9);
                break;
            case ('block'):
                $className .= '\StaticBlock';
                $value = substr($value, 6, strlen($value) - 6);
                break;
            case ('subca'):
                $className .= '\Category';
                $value = substr($value, 7, strlen($value) - 7);
                break;
            case ('widge'):
                $className .= '\Widget';
                $value = substr($value, 7, strlen($value) - 7);
                break;
            case ('video'):
                $className .= '\Video';
                $value = substr($value, 6, strlen($value) - 6);
                break;
        }
        $element = new $className();
        $element->setValue($value);
        return $element;
    }
}
