<!DOCTYPE html>
<html dir="ltr" xmlns="http://www.w3.org/1999/xhtml" lang="{{ App::getLocale() }}" class="no-js">
<head>
	<meta charset="utf-8">
	<title>{{ $world->getName() }} | Embedded WvW World Stats | GW2 Treasures</title>
	
	<link rel="stylesheet" href="{{ Helper::cdn('assets/css/normalize.min.css') }}">
	<link rel="stylesheet" href="{{ Helper::cdn('assets/css/main.css') }}">
	@if( App::environment('production') )
		<link rel="stylesheet" href="{{ Helper::cdn('assets/css/style.css') }}">
	@else
		<link rel="stylesheet" href="//direct.darthmaim-cdn.de/gw2treasures/assets/css/style.css">
	@endif
</head>
<?php
	$style = isset($_GET['style']) && in_array( $_GET['style'], array( 'light', 'dark' ))
		? $_GET['style']
		: 'light';
?>
<body class="{{ $style }}" style="min-width: 500px; min-height: 100px">
<div class="matchList">
	<div class="matchListHeader clearfix">
		<span class="world">{{ trans('wvw.world') }}</span>
		<span class="score">{{ trans('wvw.score') }}</span>
		<span class="income">{{ trans('wvw.income') }}</span>
		<span class="objectives">
			<span><i class="sprite-20-camp-gray"></i></span>
			<span><i class="sprite-20-tower-gray"></i></span>
			<span><i class="sprite-20-keep-gray"></i></span>
			<span><i class="sprite-20-castle-gray"></i></span>
		</span>
	</div>
	@include( 'wvw.smallMatchBox', array( 'match' => $world->currentMatch()->withWorlds()->first(), 'homeworld' => $world, 'embedded' => true ))
</div>
@if(Request::has('_redirectedFromOldDomain'))
	<div class="oldDomain">
		<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"><path fill="#FF7200" d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/></svg>
		<div class="oldDomainContent">
			This WvW widget is included from our old domain. If you are the admin of this site,
			please update the embed code. <a target="_blank" href="{{ route('dev.embedWorldStats.update', ['language' => App::getLocale(), 'world' => $world->id, 'style' => $style]) }}">more</a></div>
	</div>
	<style>
		.oldDomainContent {
			display: none;
		}
		.oldDomain {
			position: absolute;
			right: 4px;
			bottom: 4px;
			float: right;
			max-width: 250px;
			padding: 8px;
			font-size: 11px;
		}
		.oldDomain:hover {
			background: rgba(255,255,255,.9);
			color: #222;
			border: 1px solid black;
		}
		.oldDomain:hover svg {
			display: none
		}
		.oldDomain:hover .oldDomainContent {
			display: inline-block;
		}
	</style>
@endif
@if( App::environment( 'production' ) && !isset( $_GET['notrack'] ))
	<!-- google analytics -->
	<script>
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

		ga('create', '{{ Config::get('app.trackingCode') }}', '{{ Config::get('app.domain') }}');
		ga('send', 'pageview');
	</script>
@endif
</body>
</html>
