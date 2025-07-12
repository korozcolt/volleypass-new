<?php

namespace App\Traits;

trait EnumHelpers
{
    /**
     * Obtiene todos los valores del enum como array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Obtiene todas las etiquetas del enum como array
     */
    public static function labels(): array
    {
        $labels = [];
        foreach (self::cases() as $case) {
            $labels[$case->value] = $case->getLabel();
        }
        return $labels;
    }

    /**
     * Obtiene un array asociativo valor => etiqueta para selects
     */
    public static function forSelect(): array
    {
        return self::labels();
    }

    /**
     * Obtiene el badge HTML completo con icono
     */
    public function getBadgeHtml(): string
    {
        $icon = $this->getIcon();
        $iconHtml = $icon ? '<svg class="w-4 h-4 mr-1" fill="currentColor"><use href="#'.$icon.'"></use></svg>' : '';

        return '<span class="inline-flex items-center py-1 px-3 rounded-full text-xs font-medium '.$this->getColorHtml().'">
                    '.$iconHtml.$this->getLabel().'
                </span>';
    }

    /**
     * Obtiene la descripción extendida del enum (si existe)
     */
    public function getDescription(): ?string
    {
        if (method_exists($this, 'getDescriptionText')) {
            return $this->getDescriptionText();
        }
        return null;
    }

    /**
     * Verifica si el enum es de un tipo específico
     */
    public function is(string|array $types): bool
    {
        if (is_string($types)) {
            return $this->value === $types;
        }

        return in_array($this->value, $types);
    }

    /**
     * Verifica si el enum NO es de un tipo específico
     */
    public function isNot(string|array $types): bool
    {
        return !$this->is($types);
    }
}
