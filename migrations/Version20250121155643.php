<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250121155643 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Resetea la tabla nurse, elimina datos, reinicia el contador de ID e inserta registros en castellano.';
    }

    public function up(Schema $schema): void
    {
        // Eliminar todos los datos de la tabla
        $this->addSql('DELETE FROM nurse');

        // Reiniciar el contador de ID
        $this->addSql('ALTER TABLE nurse AUTO_INCREMENT = 1');

        // Insertar registros en castellano
        $this->addSql("
            INSERT INTO nurse (username, password, name, surname, speciality, shift, phone) VALUES
            ('ajimenez', 'contrasegura', 'Antonio', 'Jiménez', 'Pediatría', 'Mañana', '555-111-222'),
            ('mrodriguez', 'clave123', 'María', 'Rodríguez', 'Cardiología', 'Tarde', '555-222-333'),
            ('lgarcia', 'seguro2023', 'Laura', 'García', 'Geriatría', 'Noche', '555-333-444'),
            ('jperez', 'pass456', 'Juan', 'Pérez', 'Neurología', 'Mañana', '555-444-555');
        ");
    }

    public function down(Schema $schema): void
    {
        // En caso de revertir la migración, elimina los datos y reinicia el contador de ID
        $this->addSql('DELETE FROM nurse');
        $this->addSql('ALTER TABLE nurse AUTO_INCREMENT = 1');
    }
}
