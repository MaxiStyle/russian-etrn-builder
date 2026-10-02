<?php

namespace MaxiStyle\EtrnBuilder\Entities;

/**
 * Сущность "ОпГруз" — описание груза для ЭЗЗ T1.
 *
 * По XSD: атрибуты @НаимГруз, @СостГруз, @ВидТар, @ДрОГруз?, @КолГрМест, @МетОпрМасс, @Объем,
 * @РаспрГр, @ДелГр, @УчВодПогрГр?, @ПрГрКонт?; дочерние МасГруз[@МасНетЗнач?, @МасБрутЗнач, @КолПалл?,
 * @МасТар?], РазмерГрМест[@ВысЗнач, @ДлЗнач, @ШирЗнач], ИнфОпасн?.
 */
class OrderCargo extends BaseEntity
{
    /** @var string Отгрузочное наименование груза (@НаимГруз, 1..2000 символов) */
    public string $name = '';

    /** @var string Состояние груза (@СостГруз) */
    public string $condition = '';

    /** @var string Вид тары (@ВидТар, 2 знака; '00' — без тары) */
    public string $packagingType = '';

    /** @var ?string Другие сведения о грузе (@ДрОГруз) */
    public ?string $otherInfo = null;

    /** @var int Количество грузовых мест (@КолГрМест); для навалочных — 1 */
    public int $units = 1;

    /** @var string Метод определения массы (@МетОпрМасс): '01'/'02'/'03' */
    public string $weightMethod = '01';

    /** @var float Объём в м³ (@Объем, N(5.2)) */
    public float $volume = 0.0;

    /** @var bool Распределяемый груз (@РаспрГр) */
    public bool $distributable = false;

    /** @var bool Делимый груз (@ДелГр) */
    public bool $divisible = false;

    /** @var ?bool Участие водителя в погрузке (@УчВодПогрГр, только для контейнеров) */
    public ?bool $driverParticipatesLoading = null;

    /** @var ?bool Признак контейнерного груза (@ПрГрКонт) */
    public ?bool $containerCargo = null;

    /** @var ?float Масса нетто, кг (МасГруз@МасНетЗнач) */
    public ?float $netWeight = null;

    /** @var float Масса брутто, кг (МасГруз@МасБрутЗнач) */
    public float $grossWeight = 0.0;

    /** @var ?int Количество паллет (МасГруз@КолПалл) */
    public ?int $pallets = null;

    /** @var ?float Масса тары, кг (МасГруз@МасТар) */
    public ?float $tareWeight = null;

    /** @var float Высота мест, м (РазмерГрМест@ВысЗнач, N(5.3)) */
    public float $height = 0.0;

    /** @var float Длина мест, м (РазмерГрМест@ДлЗнач, N(5.3)) */
    public float $length = 0.0;

    /** @var float Ширина мест, м (РазмерГрМест@ШирЗнач, N(5.3)) */
    public float $width = 0.0;

    /** @var ?DangerousCargoInfo Сведения об опасном грузе (ИнфОпасн) */
    public ?DangerousCargoInfo $dangerous = null;
}