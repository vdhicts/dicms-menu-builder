<?php

namespace Vdhicts\Dicms\Menu\Renderers;

use Vdhicts\Dicms\Html;
use Vdhicts\Dicms\Menu\Contracts;
use Vdhicts\Dicms\Menu\Item;
use Vdhicts\Dicms\Menu\ItemCollection;

class ListInline implements Contracts\Renderer
{
    /**
     * Renders a single item.
     * @param Item $item
     * @return Html\Element
     */
    public function generateItem(Item $item): Html\Element
    {
        $listGroupItem = new Html\Element('li');

        // When the item is a divider, just add the class and we're done
        if ($item->isDivider()) {
            $listGroupItem->addAttributeValue('class', 'list-inline-divider');
            return $listGroupItem;
        }

        // Create a link if the item has a link and isn't a divider
        if ($item->hasLink()) {
            $link = new Html\Element('a', $item->getName(), ['href' => $item->getLink()]);
            $listGroupItem->inject($link);
        } else { // Otherwise the item is just a div
            $listGroupItem->setText($item->getName());
        }

        return $listGroupItem;
    }

    /**
     * Renders the menu.
     * @param ItemCollection $itemCollection
     * @param mixed $parentId
     * @return string
     */
    public function generate(ItemCollection $itemCollection, $parentId = null): string
    {
        $list = new Html\Element('ul', '', ['class' => 'list-inline']);
        foreach ($itemCollection->getChildren($parentId) as $itemId) {
            $listItem = $this->generateItem($itemCollection->getItem($itemId));

            // Generate children recursively
            $hasChildren = $itemCollection->hasChildren($itemId);
            if ($hasChildren) {
                $listItem->addText($this->generate($itemCollection, $itemId));
            }

            $list->inject($listItem);
        }
        return $list->generate();
    }
}
