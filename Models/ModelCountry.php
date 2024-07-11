<?php

declare(strict_types=1);

namespace Plugin\landswitcher\Models;

use Exception;
use JTL\Model\DataAttribute;
use JTL\Model\DataModel;

/**
 * Class ModelItem
 * This class is generated by shopcli model:create
 *
 * @package Plugin\jtl_test
 * @property int    $id
 * @method int getId()
 * @method void setId(int $value)
 * @property string $slug
 * @method string getSlug()
 * @method void setSlug(string $slug)
 * @property string $description
 * @method string getDescription()
 * @method void setDescription(string $description)
 * @property string $name
 * @method string getName()
 * @method void setName(string $name)
 */
final class ModelCountry extends DataModel
{
    /**
     * @inheritdoc
     */
    public function getTableName(): string
    {
        return 'tland';
    }

    /**
     * Setting of keyname is not supported!
     * Call will always throw an Exception with code ERR_DATABASE!
     * @inheritdoc
     */
    public function setKeyName($keyName): void
    {
        throw new Exception(__METHOD__ . ': setting of keyname is not supported', self::ERR_DATABASE);
    }

    public function getAttributes(): array
    {
        static $attributes = null;
        if ($attributes === null) {
            $attributes = [];

            $cISO = DataAttribute::create('cISO', 'varchar', null, false, true);
            $cISO->getInputConfig()->setModifyable(false);
            $attributes['CISO'] = $cISO;

            $eng = DataAttribute::create('cEnglisch', 'varchar', null, false, false);
            $eng->getInputConfig()->setModifyable(false);
            $attributes['name'] = $eng;
        }

        return $attributes;
    }
}
