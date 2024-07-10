<?php

declare(strict_types=1);

namespace Plugin\landswitcher\Migrations;

use JTL\Plugin\Migration;
use JTL\Update\IMigration;

/**
 * Class Migration20240710082231
 * @package Plugin\landswitcher\Migrations
 */
class Migration20240710082231 extends Migration implements IMigration
{
    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            'CREATE TABLE IF NOT EXISTS `landswitcher_redirects` (
              `id` INT NOT NULL AUTO_INCREMENT,
              `url` VARCHAR(255) NOT NULL,
              `country` VARCHAR(5) NOT NULL,
              PRIMARY KEY (`id`),
              FOREIGN KEY (`country`) REFERENCES `tland` (`cISO`)
              ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci'
        );
        $this->execute(
            "INSERT INTO `landswitcher_redirects` (`url`, `country`)
                VALUES ('https://site.com/en', 'GB'), ('/', 'DE');"
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        if ($this->doDeleteData()) {
            $this->execute('DROP TABLE IF EXISTS `landswitcher_redirects`');
        }
    }
}
