<?php

namespace MaxiStyle\EtrnBuilder\Entities;

/**
 * Сущность "ИнфПол" (ИнфПолТип) — информационное поле произвольного назначения.
 * Атрибуты: @ИдФайлИнфПол, @ТекстИнф.
 */
class InfoField extends BaseEntity
{
    /** @var string Идентификатор информационного поля (@ИдФайлИнфПол) */
    public string $fileId = '';

    /** @var string Текст (@ТекстИнф) */
    public string $text = '';
}