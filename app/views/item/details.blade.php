<header class="itemHeader">
	<img class="icon" width="64" height="64" src="<?= $item->getIconUrl() ?>" alt="">
	<h2>{{ $item->getName( ) }}</h2>
	<nav>
		@if( App::getLocale() != 'de' )
			<div class="lang">[<span title="Deutsch"  class='langCode'>de</span>] <a rel="alternate" hreflang="de" href="{{ $item->getUrl('de') }}" data-item-id="{{ $item->id }}">{{ $item->getName( 'de' ) }}</a></div>
		@endif
		@if( App::getLocale() != 'en' )
			<div class="lang">[<span title="English"  class='langCode'>en</span>] <a rel="alternate" hreflang="en" href="{{ $item->getUrl('en') }}" data-item-id="{{ $item->id }}">{{ $item->getName( 'en' ) }}</a></div>
		@endif
		@if( App::getLocale() != 'es' )
			<div class="lang">[<span title="Español"  class='langCode'>es</span>] <a rel="alternate" hreflang="es" href="{{ $item->getUrl('es') }}" data-item-id="{{ $item->id }}">{{ $item->getName( 'es' ) }}</a></div>
		@endif
		@if( App::getLocale() != 'fr' )
			<div class="lang">[<span title="Français" class='langCode'>fr</span>] <a rel="alternate" hreflang="fr" href="{{ $item->getUrl('fr') }}" data-item-id="{{ $item->id }}">{{ $item->getName( 'fr' ) }}</a></div>
		@endif
	</nav>
</header>

<dl class="infobox">
	<dt>Chatlink</dt>
	<dd><input type="text" class="chatlink" readonly value="{{ e( $item->getChatLink() ) }}" /></dd>
	<dt>Wiki</dt>
	<dd>
		<a target="_blank" onclick="outbound(this)" href="http://wiki-de.guildwars2.com/index.php?title=Spezial:Suche&amp;search={{ urlencode( $item->getName( 'de' ) ) }}">German</a>
		<a target="_blank" onclick="outbound(this)" href="http://wiki.guildwars2.com/index.php?title=Special:Search&amp;search={{ urlencode( $item->getName( 'en' ) ) }}">English</a>
		<a target="_blank" onclick="outbound(this)" href="http://wiki-es.guildwars2.com/index.php?title=Especial:Buscar&amp;search={{ urlencode( $item->getName( 'es' ) ) }}">Spanish</a>
		<a target="_blank" onclick="outbound(this)" href="http://wiki-fr.guildwars2.com/index.php?title=Spécial:Recherche&amp;search={{ urlencode( $item->getName( 'fr' ) ) }}">French</a>
	</dd>
	<dt>Trading Post Info</dt>
	<dd>
		<a target="_blank" onclick="outbound(this)" href="http://www.gw2spidy.com/item/{{ $item->id }}">Guild Wars 2 Spidy</a>
	</dd>
</dl>

{{ $item->getTooltip() }}

@if( $item->type == 'UpgradeComponent' && count( $upgradeFor = Item::hasUpgrade( $item )->get()) > 0 )
	<?php 
		$upgradeFor->sort( function( $a, $b ) use ( $item ) {
			return strcmp( $a->getName(), $b->getName() );
		});
	?>
	<h3>{{ trans('item.upgradeFor') }}</h3>
	<ul class="itemList">
		@foreach ($upgradeFor as $usesThisAsUpgrade)
			<li><a data-item-id="{{ $usesThisAsUpgrade->id }}" href="{{ $usesThisAsUpgrade->getUrl() }}">
				<img src="{{ $usesThisAsUpgrade->getIconUrl( 32 ) }}" width="32" height="32" alt="">
				{{ $usesThisAsUpgrade->getName() }}
			</a>
		@endforeach
	</ul>
@endif

@if( $item->unlock_type == 'CraftingRecipe' && !is_null( $item->unlocks ))
	<h3>{{ trans('item.unlocks') }}</h3>
	@include( 'recipe.box', array( 'recipe' => $item->unlocks ))
@endif

@if( count( $craftedFrom = $item->recipes()->get()) > 0 )
	<h3>{{ trans('item.craftedFrom') }}</h3>
	@foreach( $craftedFrom as $recipe )
		@include( 'recipe.box', array( 'recipe' => $recipe ))
	@endforeach
@endif

@if( count( $usedInCrafting = $item->ingredientForRecipes()->orderBy( 'rating' )->orderBy( 'disciplines' )->get()) > 0 )
	<h3>{{ trans('item.usedInCrafting') }}</h3>
	@include( 'recipe.table', array( 'recipes' => $usedInCrafting ))
@endif

@if( count( $similarItems = $item->getSimilarItems()) > 0 )
	<?php 
		$similarItems->sort( function( $a, $b ) use ( $item ) {
			if( $a->getName() == $item->getName() && 
			    $b->getName() != $item->getName() ) {
				return -1;
			}
			if( $a->getName() != $item->getName() && 
			    $b->getName() == $item->getName() ) {
				return 1;
			}
			return strcmp( $a->getName(), $b->getName() );
		});
		$hideSomeSimilarItems = count( $similarItems ) > 20;
	?>
	<h3>{{ trans('item.similar') }}</h3>
	<ul class="itemList" id="similar">
		@for( $i = 0; $i < count( $similarItems ) && ( $hideSomeSimilarItems ? $i < 9 : true ); $i++ )
			<?php $similarItem = $similarItems[ $i ] ?>
			<li>{{ $similarItem->link( 32 ) }}
		@endfor
		@if( $hideSomeSimilarItems )
			<li class="showMore"><a href="#similar">
				<span style="display:inline-block; width:32px; height:32px; vertical-align: middle"></span>
				{{ trans( 'item.showMoreSimilarItems', array( 'count' => count( $similarItems ) - 9 )) }}</a>
			@for( $i = 9; $i < count( $similarItems ); $i++ )
				<?php $similarItem = $similarItems[ $i ] ?>
				<li class="similarHidden">{{ $similarItem->link( 32 ) }}
		@endfor
		@endif
	</ul>

	<!-- remove style once the production server uses https://github.com/darthmaim/gw2treasures-assets -->
	<style type="text/css">
		.similarHidden { display: none; }
		#similar:target .similarHidden { display: list-item; }
		#similar:target .showMore { display: none; }
	</style>
@endif