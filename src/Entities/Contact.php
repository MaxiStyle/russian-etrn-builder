<?php

namespace MaxiStyle\EtrnBuilder\Entities;

/**
 * Сущность "Контакт"
 * Контактные данные
 */
class Contact extends BaseEntity
{
    /**
     * @var ?string Номер контактного телефона (одиночное значение, используется в ЭТрН T1..T4).
     * Для ЭЗЗ, где @Тлф ОМ, используйте {@see $phones}.
     */
    public ?string $phone = null;

    /**
     * @var ?string Адрес электронной почты
     */
    public ?string $email = null;

    /**
     * @var ?string Иные контактные данные
     */
    public ?string $other = null;

    /**
     * @var string[] Телефоны (несколько значений). Если заполнено, билдер выводит несколько <Тлф>;
     * иначе используется одиночный {@see $phone}. Введено для ЭЗЗ (Участник/Конт, XSD ФНС).
     */
    public array $phones = [];

    /**
     * @var string[] E-mail'ы (несколько значений). Если заполнено, билдер выводит несколько <ЭлПочта>;
     * иначе используется одиночный {@see $email}. Введено для ЭЗЗ.
     */
    public array $emails = [];
}
