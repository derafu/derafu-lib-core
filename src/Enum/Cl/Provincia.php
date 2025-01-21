<?php

declare(strict_types=1);

/**
 * Derafu: Biblioteca PHP (Núcleo).
 * Copyright (C) Derafu <https://www.derafu.org>
 *
 * Este programa es software libre: usted puede redistribuirlo y/o modificarlo
 * bajo los términos de la Licencia Pública General Affero de GNU publicada por
 * la Fundación para el Software Libre, ya sea la versión 3 de la Licencia, o
 * (a su elección) cualquier versión posterior de la misma.
 *
 * Este programa se distribuye con la esperanza de que sea útil, pero SIN
 * GARANTÍA ALGUNA; ni siquiera la garantía implícita MERCANTIL o de APTITUD
 * PARA UN PROPÓSITO DETERMINADO. Consulte los detalles de la Licencia Pública
 * General Affero de GNU para obtener una información más detallada.
 *
 * Debería haber recibido una copia de la Licencia Pública General Affero de GNU
 * junto a este programa.
 *
 * En caso contrario, consulte <http://www.gnu.org/licenses/agpl.html>.
 */

namespace Derafu\Lib\Core\Enum\Cl;

use RuntimeException;

/**
 * Enum para representar las provincias de Chile.
 */
enum Provincia: int
{
    case ARICA = 151;
    case PARINACOTA = 152;
    case IQUIQUE = 11;
    case TAMARUGAL = 14;
    case ANTOFAGASTA = 21;
    case EL_LOA = 22;
    case TOCOPILLA = 23;
    case COPIAPO = 31;
    case CHANARAL = 32;
    case HUASCO = 33;
    case ELQUI = 41;
    case CHOAPA = 42;
    case LIMARI = 43;
    case VALPARAISO = 51;
    case MARGA_MARGA = 58;
    case ISLA_DE_PASCUA = 52;
    case LOS_ANDES = 53;
    case PETORCA = 54;
    case QUILLOTA = 55;
    case SAN_ANTONIO = 56;
    case SAN_FELIPE = 57;
    case CACHAPOAL = 61;
    case CARDENAL_CARO = 62;
    case COLCHAGUA = 63;
    case TALCA = 71;
    case CAUQUENES = 72;
    case CURICO = 73;
    case LINARES = 74;
    case CONCEPCION = 81;
    case ARAUCO = 82;
    case BIO__BIO = 83;
    case DIGUILLIN = 161;
    case ITATA = 162;
    case PUNILLA = 163;
    case CAUTIN = 91;
    case MALLECO = 92;
    case VALDIVIA = 141;
    case RANCO = 142;
    case LLANQUIHUE = 101;
    case CHILOE = 102;
    case OSORNO = 103;
    case PALENA = 104;
    case COIHAIQUE = 111;
    case AISEN = 112;
    case CAPITAN_PRAT = 113;
    case GENERAL_CARRERA = 114;
    case MAGALLANES = 121;
    case ANTARTICA_CHILENA = 122;
    case TIERRA_DEL_FUEGO = 123;
    case ULTIMA_ESPERANZA = 124;
    case SANTIAGO = 131;
    case CORDILLERA = 132;
    case CHACABUCO = 133;
    case MAIPO = 134;
    case MELIPILLA = 135;
    case TALAGANTE = 136;

    private const GLOSAS = [
        self::ARICA->value => 'Arica',
        self::PARINACOTA->value => 'Parinacota',
        self::IQUIQUE->value => 'Iquique',
        self::TAMARUGAL->value => 'Tamarugal',
        self::ANTOFAGASTA->value => 'Antofagasta',
        self::EL_LOA->value => 'El Loa',
        self::TOCOPILLA->value => 'Tocopilla',
        self::COPIAPO->value => 'Copiapó',
        self::CHANARAL->value => 'Chañaral',
        self::HUASCO->value => 'Huasco',
        self::ELQUI->value => 'Elqui',
        self::CHOAPA->value => 'Choapa',
        self::LIMARI->value => 'Limari',
        self::VALPARAISO->value => 'Valparaíso',
        self::MARGA_MARGA->value => 'Marga Marga',
        self::ISLA_DE_PASCUA->value => 'Isla de Pascua',
        self::LOS_ANDES->value => 'Los Andes',
        self::PETORCA->value => 'Petorca',
        self::QUILLOTA->value => 'Quillota',
        self::SAN_ANTONIO->value => 'San Antonio',
        self::SAN_FELIPE->value => 'San Felipe',
        self::CACHAPOAL->value => 'Cachapoal',
        self::CARDENAL_CARO->value => 'Cardenal Caro',
        self::COLCHAGUA->value => 'Colchagua',
        self::TALCA->value => 'Talca',
        self::CAUQUENES->value => 'Cauquenes',
        self::CURICO->value => 'Curico',
        self::LINARES->value => 'Linares',
        self::CONCEPCION->value => 'Concepción',
        self::ARAUCO->value => 'Arauco',
        self::BIO__BIO->value => 'Bío- Bío',
        self::DIGUILLIN->value => 'Diguillín',
        self::ITATA->value => 'Itata',
        self::PUNILLA->value => 'Punilla',
        self::CAUTIN->value => 'Cautín',
        self::MALLECO->value => 'Malleco',
        self::VALDIVIA->value => 'Valdivia',
        self::RANCO->value => 'Ranco',
        self::LLANQUIHUE->value => 'Llanquihue',
        self::CHILOE->value => 'Chiloe',
        self::OSORNO->value => 'Osorno',
        self::PALENA->value => 'Palena',
        self::COIHAIQUE->value => 'Coihaique',
        self::AISEN->value => 'Aisén',
        self::CAPITAN_PRAT->value => 'Capitan Prat',
        self::GENERAL_CARRERA->value => 'General Carrera',
        self::MAGALLANES->value => 'Magallanes',
        self::ANTARTICA_CHILENA->value => 'Antártica Chilena',
        self::TIERRA_DEL_FUEGO->value => 'Tierra del Fuego',
        self::ULTIMA_ESPERANZA->value => 'Ultima Esperanza',
        self::SANTIAGO->value => 'Santiago',
        self::CORDILLERA->value => 'Cordillera',
        self::CHACABUCO->value => 'Chacabuco',
        self::MAIPO->value => 'Maipo',
        self::MELIPILLA->value => 'Melipilla',
        self::TALAGANTE->value => 'Talagante',
    ];

    /**
     * Entrega el código de la provincia normalizado a 3 dígitos.
     *
     * @return string
     */
    public function getCodigo(): string
    {
        return $this->value < 100 ? '0' . $this->value : (string) $this->value;
    }

    /**
     * Obtiene la glosa asociada a una provincia.
     *
     * @return string
     */
    public function getGlosa(): string
    {
        return self::GLOSAS[$this->value];
    }

    /**
     * Obtiene el nombre de la provincia.
     *
     * @return string
     */
    public function getNombre(): string
    {
        return $this->getGlosa();
    }

    /**
     * Entrega la región a la que pertenece la provincia.
     *
     * Los 2 primeros dígitos del código normalizado de la provincia
     * corresponden al código normalizado de la región.
     *
     * @return Region
     */
    public function getRegion(): Region
    {
        $regionCode = (int) substr($this->getCodigo(), 0, 2);
        $region = Region::tryFrom($regionCode);

        if ($region === null) {
            throw new RuntimeException(sprintf(
                'No se encontró la región para la provincia %s.',
                $this->getNombre()
            ));
        }

        return $region;
    }

    /**
     * Entrega las comunas asociadas a la provincia.
     *
     * Los 3 primeros dígitos del código normalizado de la comuna
     * corresponden al código normalizado de la provincia.
     *
     * @return Comuna[]
     */
    public function getComunas(): array
    {
        return array_filter(
            Comuna::cases(),
            fn ($comuna) =>
                substr($comuna->getCodigo(), 0, 3) === $this->getCodigo()
        );
    }
}
