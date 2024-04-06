<?php
declare(strict_types=1);

namespace Isobar\Megamenu\Model\Backend\SaveLink;

/**
 * Class Pool
 * @package Isobar\Megamenu\Model\Backend\SaveLink
 */
class Pool
{
    /**
     * @var array
     */
    private $modifiers;

    /**
     * Pool constructor.
     * @param array $modifiers
     */
    public function __construct(
        array $modifiers = []
    ) {
        $this->setModifiers($modifiers);
    }

    /**
     * Sort modifiers by sort_order and save sorted objects.
     *
     * @param array $modifiers
     * @return void
     */
    protected function setModifiers(array $modifiers): void
    {
        usort($modifiers, function (array $modifierLeft, array $modifierRight) {
            $left = $modifierLeft['sort_order'] ?? 0;
            $right = $modifierRight['sort_order'] ?? 0;

            return $left <=> $right;
        });

        $this->modifiers = array_column($modifiers, 'object');
    }

    /**
     * @param array $inputData
     * @return array
     */
    public function execute(array $inputData): array
    {
        foreach ($this->modifiers as $modifier) {
            if ($modifier instanceof DataCollectorInterface) {
                $inputData = $modifier->execute($inputData);
            }
        }

        return $inputData;
    }
}
