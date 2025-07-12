<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasIcon;

enum DocumentFormat: string implements HasLabel, HasColor, HasIcon {
    case PDF = 'pdf';
    case JPG = 'jpg';
    case PNG = 'png';
    case DOC = 'doc';
    case DOCX = 'docx';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PDF => 'PDF',
            self::JPG => 'JPG',
            self::PNG => 'PNG',
            self::DOC => 'DOC',
            self::DOCX => 'DOCX',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::PDF => 'danger',
            self::JPG => 'warning',
            self::PNG => 'success',
            self::DOC => 'info',
            self::DOCX => 'primary',
        };
    }

    public function getIcon(): string | null
    {
        return match ($this) {
            self::PDF => 'heroicon-o-document',
            self::JPG => 'heroicon-o-photo',
            self::PNG => 'heroicon-o-camera',
            self::DOC => 'heroicon-o-document-text',
            self::DOCX => 'heroicon-o-document-text',
        };
    }

    public function getColorHtml(): ?string
    {
        return match ($this) {
            self::PDF => 'bg-red-100 text-red-800',
            self::JPG => 'bg-yellow-100 text-yellow-800',
            self::PNG => 'bg-green-100 text-green-800',
            self::DOC => 'bg-blue-100 text-blue-800',
            self::DOCX => 'bg-indigo-100 text-indigo-800',
        };
    }

    public function getLabelHtml(): ?string
    {
        return '<span class="py-1 px-3 rounded-full text-xs font-medium '.$this->getColorHtml().'">'.$this->getLabel().'</span>';
    }

    public function getMimeType(): string
    {
        return match ($this) {
            self::PDF => 'application/pdf',
            self::JPG => 'image/jpeg',
            self::PNG => 'image/png',
            self::DOC => 'application/msword',
            self::DOCX => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        };
    }

    public function getMaxSizeMB(): int
    {
        return match ($this) {
            self::PDF => 10,
            self::JPG => 5,
            self::PNG => 5,
            self::DOC => 10,
            self::DOCX => 10,
        };
    }
}
