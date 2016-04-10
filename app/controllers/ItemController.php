<?php

use GW2Treasures\GW2Tools\Chatlinks\ItemChatlink;
use GW2Treasures\GW2Tools\Chatlinks\RecipeChatlink;
use GW2Treasures\GW2Tools\Chatlinks\SkinChatlink;

class ItemController extends BaseController {
    /** @var \Illuminate\View\View|stdClass $layout  */
    protected $layout = 'layout';


    public function showDetails( $language, Item $item ) {
        $this->layout->title = $item->getName();
        $this->layout->fullWidth = true;

        $key = CacheHelper::ItemDetails( $item, $language );

        if( Cache::has( $key ) && !isset($_GET[ 'nocache' ]) ) {
            $this->layout->content = Cache::get( $key );
            $this->layout->cached = true;
        } else {
            $content = View::make( 'item.details', array( 'item' => $item ) )->render();
            $this->layout->content = $content;

            Cache::forever( $key, $content );
        }

        if( isset($item->getTypeData()->type) ) {
            $type = trans( 'item.type.' . $item->type ) . ' (' .
                    trans( 'item.subtype.' . $item->type . '.' . $item->getTypeData()->type ) . ')';
        } elseif( $item->sub_type != '' ) {
            $type = trans( 'item.type.' . $item->type ) . ' (' .
                    trans( 'item.subtype.' . $item->type . '.' . $item->subtype ) . ')';
        } else {
            $type = trans( 'item.type.' . $item->type );
        }

        $this->layout->metaTitle = $item->getName();
        $this->layout->metaImage = 'https:' . $item->getIconUrl(64);
        $this->layout->metaDescription = trans( 'item.rarity.' . $item->rarity ) . ' ' . $type;
        $this->layout->canonical = $item->getUrl();

        DB::table('item_views')->insert(array(
            'item_id' => $item->id,
            'language' => $language,
        ));
    }
    public function json( $language, Item $item ) {
        return Response::json( $item->getData() );
    }

    public function random( $language ) {
        $id = Item::random()->first()->id;
        return Redirect::route( 'itemdetails', array( $language, $id ));
    }

    public function tooltip( $language, Item $item ) {
        return $item->getTooltip( $language );
    }

    public function search( $language ) {
        $searchTerm = trim( Input::get('q') );

        if( strlen( $searchTerm ) > 0 ) {
            // item id
            if( preg_match('/^[0-9]+$/', $searchTerm ) ) {
                $item = Item::find( intval( $searchTerm ) );
                if( !is_null( $item ) ) {
                    return Redirect::route( 'itemdetails', array( $language, $searchTerm ));
                }
            }

            try {
                $chatlink = \GW2Treasures\GW2Tools\Chatlinks\Chatlink::decode($searchTerm);
            } catch (Exception $e) {
                $chatlink = false;
            }

            if($chatlink instanceof SkinChatlink) {
                return Redirect::route('skin.details', [$language, $chatlink->getId()]);
            }
        }

        $query = new SearchQuery($searchTerm);

        $this->layout->content = $query->renderResults();
        $this->layout->fullWidth = true;
        $this->layout->title = 'Search Results';
    }

    public function searchAutocomplete( $language ) {
        $searchTerm = trim( Input::get('q') );

        try {
            $chatlink = \GW2Treasures\GW2Tools\Chatlinks\Chatlink::decode($searchTerm);
        } catch(Exception $e) {
            $chatlink = false;
        }

        $items = [];

        if($chatlink !== false ) {
            if($chatlink instanceof ItemChatlink) {
                $itemstack = $chatlink->getItemStack();
                $ids = $itemstack->upgrades;
                $ids[] = $itemstack->id;
                $items = Item::whereIn('id', $ids)->get();
            } elseif($chatlink instanceof RecipeChatlink) {
                $items = [Recipe::find($chatlink->getId())->output];
            }
        } else {
            $items = Item::search( $searchTerm )->take( 10 )->get();
        }

        $response = new stdClass();
        $response->items = array();

        foreach ( $items as $item ) {
            if( is_null($item) ) {
                continue;
            }
            $i = new stdClass();
            $i->id = $item->id;
            $i->name = $item->getName();
            $i->icon16 = $item->getIconUrl(16);
            $i->icon32 = $item->getIconUrl(32);
            $i->icon64 = $item->getIconUrl(64);
            $response->items[] = $i;
        }

        return Response::json( $response );
    }
}
