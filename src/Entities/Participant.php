<?php

namespace MaxiStyle\EtrnBuilder\Entities;

/**
 * Сущность "УчастникТип" — универсальный участник обмена ЭЗЗ (СвГО, СвПрв, ОргВладИнфр).
 * По XSD: ИдСв (СвЮЛУч | СвИП | СвФЛУчаст | СвИнНеУч), Адрес, Конт, ИнфДляУчаст*.
 *
 * Ровно одно из legalEntity/ip/individual/foreign должно быть задано (проверяется билдером).
 */
class Participant extends BaseEntity
{
    /** @var ?LegalEntity Юрлицо (СвЮЛУч) */
    public ?LegalEntity $legalEntity = null;

    /** @var ?IP Индивидуальный предприниматель (СвИП) */
    public ?IP $ip = null;

    /** @var ?Individual Физическое лицо (СвФЛУчаст) */
    public ?Individual $individual = null;

    /** @var ?ForeignEntity Иностранное лицо, не состоящее на учёте (СвИнНеУч) */
    public ?ForeignEntity $foreign = null;

    /** @var ?Address Адрес участника */
    public ?Address $address = null;

    /** @var ?Contact Контакт (Тлф ОМ, ЭлПочта НМ, ИнКонт) */
    public ?Contact $contact = null;

    /**
     * @var array<array{inn: string, text: string}> ИнфДляУчаст*: пары {ИННУчаст, ИнфУчаст}
     * для передачи дополнительной информации адресатам обмена.
     */
    public array $extraInfo = [];
}