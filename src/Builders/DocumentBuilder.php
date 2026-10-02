<?php

namespace MaxiStyle\EtrnBuilder\Builders;

use DOMDocument;
use DOMElement;
use DOMException;
use MaxiStyle\EtrnBuilder\Entities\Address;
use MaxiStyle\EtrnBuilder\Entities\Contact;
use MaxiStyle\EtrnBuilder\Entities\DocumentRequisites;
use MaxiStyle\EtrnBuilder\Entities\IP;
use MaxiStyle\EtrnBuilder\Entities\LegalEntity;
use MaxiStyle\EtrnBuilder\Entities\Signatory;

class DocumentBuilder
{
    /**
     * @throws DOMException
     */
    public function append(DOMDocument &$dom, DOMElement &$parent, string $name, ?string $value): void
    {
        if ($value !== null) {
            $parent->appendChild($dom->createElement($name, $value));
        }
    }

    /**
     * Ставит атрибут только при непустом значении (не null и не пустая строка).
     */
    protected function setAttributeIf(DOMElement $el, string $name, ?string $value): void
    {
        if ($value !== null && $value !== '') {
            $el->setAttribute($name, $value);
        }
    }

    /**
     * Создаёт и добавляет в $parent элемент <ФИО> с атрибутами Фамилия/Имя[/Отчество].
     * Отчество не выводится, если пустое.
     */
    protected function appendFio(DOMDocument $dom, DOMElement $parent, ?string $last, ?string $first, ?string $middle): DOMElement
    {
        $fio = $dom->createElement('ФИО');
        $fio->setAttribute('Фамилия', $last ?? '');
        $fio->setAttribute('Имя', $first ?? '');
        $this->setAttributeIf($fio, 'Отчество', $middle);
        $parent->appendChild($fio);
        return $fio;
    }

    /**
     * Добавляет блок <ИдСв> с <СвЮЛУч> или <СвИП> внутри.
     * Один из аргументов должен быть задан; если оба null — блок содержит только пустой <ИдСв>
     * (историческая семантика, соответствует поведению T1Builder до рефакторинга).
     */
    protected function appendIdentity(DOMDocument $dom, DOMElement $parent, ?LegalEntity $legalEntity, ?IP $ip): void
    {
        $idSv = $dom->createElement('ИдСв');
        if ($legalEntity !== null) {
            $svYuLuch = $dom->createElement('СвЮЛУч');
            $svYuLuch->setAttribute('НаимОрг', $legalEntity->name);
            $svYuLuch->setAttribute('ИННЮЛ', $legalEntity->inn);
            $svYuLuch->setAttribute('КПП', $legalEntity->kpp);
            $idSv->appendChild($svYuLuch);
        } elseif ($ip !== null) {
            $svIp = $dom->createElement('СвИП');
            $svIp->setAttribute('ИННФЛ', $ip->inn);
            $this->setAttributeIf($svIp, 'ОГРНИП', $ip->ogrnip);
            $this->setAttributeIf($svIp, 'ИныеСвед', $ip->other);
            $this->appendFio($dom, $svIp, $ip->lastName, $ip->firstName, $ip->middleName);
            $idSv->appendChild($svIp);
        }
        $parent->appendChild($idSv);
    }

    /**
     * Добавляет блок <Контакт> с дочерними <Тлф>/<ЭлПочта>/<ИнКонт> (только заполненные).
     * Если {@see Contact::$phones} / {@see Contact::$emails} не пусты — выводится несколько значений
     * (для ЭЗЗ Тлф ОМ); иначе fallback на одиночные $phone/$email (совместимость с ЭТрН T1..T4).
     */
    protected function appendContact(DOMDocument $dom, DOMElement $parent, ?Contact $contact): void
    {
        if ($contact === null) {
            return;
        }
        $el = $dom->createElement('Контакт');
        if (!empty($contact->phones)) {
            foreach ($contact->phones as $phone) {
                $this->append($dom, $el, 'Тлф', $phone);
            }
        } else {
            $this->append($dom, $el, 'Тлф', $contact->phone);
        }
        if (!empty($contact->emails)) {
            foreach ($contact->emails as $email) {
                $this->append($dom, $el, 'ЭлПочта', $email);
            }
        } else {
            $this->append($dom, $el, 'ЭлПочта', $contact->email);
        }
        $this->append($dom, $el, 'ИнКонт', $contact->other);
        $parent->appendChild($el);
    }

    /**
     * Строит inline-элемент <АдрРФ> с атрибутами адреса и вешает его на $parent
     * (используется внутри <Адрес> в СвГО/СвЗак/СвГП/СвПер).
     * Отличается от {@see appendAddressRf()}, которая строит <АдресРФ> с обёрткой АдрКоммент —
     * та применяется в АдресДостГр/ФАдресПогр/АдрВыгруз.
     */
    protected function appendInlineAddressRf(DOMDocument $dom, DOMElement $parent, Address $address): void
    {
        $addressElement = $dom->createElement('АдрРФ');
        $addressElement->setAttribute('Индекс', $address->index ?? '');
        $this->setAttributeIf($addressElement, 'КодРегион', $address->regionCode);
        $this->setAttributeIf($addressElement, 'Район', $address->district);
        $this->setAttributeIf($addressElement, 'Город', $address->city);
        $this->setAttributeIf($addressElement, 'НаселПункт', $address->settlement);
        $this->setAttributeIf($addressElement, 'Улица', $address->street);
        $this->setAttributeIf($addressElement, 'Дом', $address->house);
        $this->setAttributeIf($addressElement, 'Корпус', $address->building);
        $this->setAttributeIf($addressElement, 'Кварт', $address->flat);
        $parent->appendChild($addressElement);
    }

    /**
     * Строит блок <Подписант> с атрибутами СтатПодп/Должн? и дочерним <ФИО>.
     * Единая реализация для T1..T4: Должн выводится, только если задано; Отчество — только если задано.
     */
    protected function appendSignatory(DOMDocument $dom, DOMElement $parent, Signatory $signatory): void
    {
        $podpis = $dom->createElement('Подписант');
        $podpis->setAttribute('СтатПодп', $signatory->status);
        $this->setAttributeIf($podpis, 'Должн', $signatory->position ?? null);
        $this->appendFio(
            $dom, $podpis,
            $signatory->lastName,
            $signatory->firstName,
            $signatory->middleName !== '' ? $signatory->middleName : null,
        );
        $parent->appendChild($podpis);
    }

    /**
     * Добавляет ссылку на предыдущий файл обмена: <$wrapperName $fileIdAttr @$dateAttr @$timeAttr @ЭП>.
     * Используется для ИдИнфГО (в T2), ИдИнфПрвПрием (в T3), ИдИнфГП (в T4) — имена атрибутов у всех разные.
     * Для ИдЗак в T1 — аналогично (см. §8 ТЗ ЭЗЗ).
     */
    protected function appendReference(
        DOMDocument $dom,
        DOMElement $parent,
        string $wrapperName,
        string $fileIdAttr,
        string $dateAttr,
        string $timeAttr,
        string $fileId,
        string $date,
        string $time,
        string $signature,
    ): void {
        $ref = $dom->createElement($wrapperName);
        $ref->setAttribute($fileIdAttr, $fileId);
        $ref->setAttribute($dateAttr, $date);
        $ref->setAttribute($timeAttr, $time);
        $ref->setAttribute('ЭП', $signature);
        $parent->appendChild($ref);
    }

    /**
     * Добавляет РФ-адрес в контейнер (АдресДостГр/ФАдресПогр/АдрВыгруз):
     * - вешает АдрКоммент на сам контейнер (полная строка адреса);
     * - создаёт дочерний АдресРФ с частями (Индекс/КодРегион/Район/Город/...).
     */
    public function appendAddressRf(DOMDocument $dom, DOMElement $wrapper, Address $address): void
    {
        if (!empty($address->full)) {
            $wrapper->setAttribute('АдрКоммент', $address->full);
        }

        $adr = $dom->createElement('АдресРФ');
        $adr->setAttribute('Индекс', $address->index ?? '');
        $adr->setAttribute('КодРегион', $address->regionCode ?? '');
        $this->setAttributeIf($adr, 'Район', $address->district);
        $this->setAttributeIf($adr, 'Город', $address->city);
        $this->setAttributeIf($adr, 'НаселПункт', $address->settlement);
        $this->setAttributeIf($adr, 'Улица', $address->street);
        $this->setAttributeIf($adr, 'Дом', $address->house);
        $this->setAttributeIf($adr, 'Корпус', $address->building);
        $this->setAttributeIf($adr, 'Кварт', $address->flat);
        $wrapper->appendChild($adr);
    }

    /**
     * Добавляет ОснАрЛиз (реквизиты документа-основания аренды/лизинга/безвозмездного пользования)
     * в ТС/Прицеп. Обязателен при ТипВлад ∈ {3, 4, 5} по XSD.
     * Если номер не задан — используется «Без номера».
     */
    public function appendRentLeaseDocument(DOMDocument $dom, DOMElement $parent, ?DocumentRequisites $doc): void
    {
        if ($doc === null) {
            return;
        }

        $osn = $dom->createElement('ОснАрЛиз');
        if (!empty($doc->name)) $osn->setAttribute('НаимДок', $doc->name);
        $osn->setAttribute('НомерДок', !empty($doc->number) ? $doc->number : 'Без номера');
        if ($doc->date) $osn->setAttribute('ДатаДок', $doc->date->format('d.m.Y'));

        foreach ($doc->legalParticipants as $participant) {
            $this->appendIdRekSost($dom, $osn, (string)$participant);
        }

        $parent->appendChild($osn);
    }

    /**
     * Добавляет ИдРекСост с элементом ИННЮЛ (10 цифр) или ИННФЛ (12 цифр) в зависимости от длины ИНН.
     */
    public function appendIdRekSost(DOMDocument $dom, DOMElement $parent, string $inn): void
    {
        if ($inn === '') {
            return;
        }
        $idRekSost = $dom->createElement('ИдРекСост');
        $tag = strlen($inn) === 12 ? 'ИННФЛ' : 'ИННЮЛ';
        $this->append($dom, $idRekSost, $tag, $inn);
        $parent->appendChild($idRekSost);
    }
}