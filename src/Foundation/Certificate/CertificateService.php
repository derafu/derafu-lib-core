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
 * Debería haber recibido una copia de la Licencia Pública General Affero de
 * GNU junto a este programa.
 *
 * En caso contrario, consulte <http://www.gnu.org/licenses/agpl.html>.
 */

namespace Derafu\Lib\Core\Foundation\Certificate;

use Derafu\Lib\Core\Foundation\Certificate\Contract\CertificateServiceInterface;
use Derafu\Lib\Core\Foundation\Certificate\Contract\FakerInterface;
use Derafu\Lib\Core\Foundation\Certificate\Contract\LoaderInterface;
use Derafu\Lib\Core\Foundation\Certificate\Contract\ValidatorInterface;
use Derafu\Lib\Core\Foundation\Certificate\Entity\Certificate;

/**
 * Servicio que gestiona todo lo asociado a certificados digitales (aka: firmas
 * electrónicas).
 */
class CertificateService implements CertificateServiceInterface
{
    /**
     * Instancia que permite cargar (crear) certificados a partir de sus datos.
     *
     * @var LoaderInterface
     */
    private LoaderInterface $loader;

    /**
     * Instancia que permite validar un certificado digital.
     *
     * @var ValidatorInterface
     */
    private ValidatorInterface $validator;

    /**
     * Instancia que permite generar certificados autofirmados.
     *
     * @var FakerInterface
     */
    private FakerInterface $faker;

    /**
     * {@inheritdoc}
     */
    public function createFromFile(string $filepath, string $password): Certificate
    {
        return $this->loader->createFromFile($filepath, $password);
    }

    /**
     * {@inheritdoc}
     */
    public function createFromData(string $data, string $password): Certificate
    {
        return $this->loader->createFromData($data, $password);
    }

    /**
     * {@inheritdoc}
     */
    public function createFromArray(array $data): Certificate
    {
        return $this->loader->createFromArray($data);
    }

    /**
     * {@inheritdoc}
     */
    public function createFromKeys(string $publicKey, string $privateKey): Certificate
    {
        return $this->loader->createFromKeys($publicKey, $privateKey);
    }

    /**
     * {@inheritdoc}
     */
    public function validate(Certificate $certificate): void
    {
        $this->validator->validate($certificate);
    }

    /**
     * {@inheritdoc}
     */
    public function createFake(
        string $id,
        string $name,
        string $email
    ): Certificate {
        $faker = clone $this->faker;

        $faker->setSubject(
            serialNumber: $id,
            CN: $name,
            emailAddress: $email
        );

        return $faker->create();
    }
}
