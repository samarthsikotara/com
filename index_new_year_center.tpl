{literal}
    <style type="text/css">
        .tabs-div{width: 525px;
            float: left;
            margin-left: 25px;}
        .left_side_homebar{float:left;width:190px;}
        .tabs-div .home-header-row ul{width:780px;margin-left: 10px;}
        .active{background-color: #048aed !important; color:white !important;}
        .home-header-row ul.home-page-tab li{display: inline-block;
            width: 140px;
            font-size: 16px;
            text-transform: uppercase;
            text-align: center;
            padding: 10px 0px;
            color: #666;box-shadow: 0px 0px 4px #ddd;
            cursor: pointer;border-radius: 2px;
            margin-right: 5px;font-family: "Open Sans";font-weight: 400;}
        .tab-content{float:left;margin: 10px;}
        .home-right-tab-content{float:left;width:500px;padding: 10px 0;}
        .top-box
        {
            width:213px;height:543px;
            float: left;margin-top: 19px;
            margin-left: 15px;
        }
        .top-box1
        {
            width:213px;height:742px;
            float: left;margin-top: 9px;
            margin-left: 15px;
        }
        .top-box2
        {
            width:213px;height:540px;
            float: left;margin-top: 9px;
            margin-left: 15px;
        }
        .top-user-box{
            box-shadow: 0px 0px 4px #ddd;
            font-family: "Open Sans";
            font-weight: 400;
            width:100%;height:100%;
            background-color:#fbfbfb;
        }
        .top-user-box>span:first-child{
            font-size: 16px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f1f1;
            padding-top: 17px;
            float:left;
            margin-left: 14px;
            padding-left: 4px;
            padding-bottom: 9px;
        }
        .active sup {
            color: white!important;
        }
        #quip-browse-bar {
            background: #333;
            text-align: center;
            opacity: 0.5;
        }
        .alert-bar.transitionable {
            bottom: 0;
        }
        .alert-bar.transitionable {
            -webkit-transition: all 300ms ease-in-out;
            -moz-transition: all 300ms ease-in-out;
            transition: all 300ms ease-in-out;
        }
        .alert-bar {
            padding: 26px 0;
            background: #333;
            color: #fff;
            position: fixed;
            bottom: -70px;
            left: 0;
            right: 0;
            z-index: 10000;
            -moz-box-shadow: #222 0 2px 8px 0 inset;
            -webkit-box-shadow: #222 0 2px 8px 0 inset;
            -o-box-shadow: #222 0 2px 8px 0 inset;
            box-shadow: #222 0 2px 8px 0 inset;
        }
        #quip-browse-bar span {
            font-size: 16px;
            font-weight: bold;
            vertical-align: middle;
        }
        span, small, time, strike, sub, sup, a {
            display: inline;
            font-family: inherit;
        }
        #quip-browse-bar .primary.button {
            margin-left: 8px;
            margin-bottom: 0;
        }
        .button.primary, .button.primary:hover, .button.primary:active, .button-light.primary, .button-light.primary:hover, .button-light.primary:active, a.button.primary, a.button.primary:hover, a.button.primary:active {
            color: #fff !important;
        }
        .button.primary, .button-light.primary, a.button.primary {
            -moz-box-shadow: rgba(255,255,255,0.2) 1px 1px 0 0 inset;
            -webkit-box-shadow: rgba(255,255,255,0.2) 1px 1px 0 0 inset;
            -o-box-shadow: rgba(255,255,255,0.2) 1px 1px 0 0 inset;
            box-shadow: rgba(255,255,255,0.2) 1px 1px 0 0 inset;
            border: 1px solid rgba(0,0,0,0.15);
            background: #c71f24;
            background: -webkit-gradient(linear, left top, left bottom, from(#e0393e), to(#c71f24));
            background: -moz-linear-gradient(top, #e0393e, #c71f24);
            filter: progid:DXImageTransform.Microsoft.Gradient(GradientType=0, startColorstr='#FFE0393E', endColorstr='#FFC71F24');
            -ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr='#FFE0393E', endColorstr='#FFC71F24')";
        }

    </style>
{/literal}
<div class="left_side_homebar">
    {include file="../templates/wistie/homepage_sidebar.tpl"}
</div>
<div class="tabs-div">
    <div class="home-header-row">
        <ul id="plan-page-tabs" class="home-page-tab">
            <li divId="home-tab-content-events" class="box-style active-buttons{if $tab eq ""} active"{/if} id="home-tab-people">Plans</li>
            <li divId="home-tab-content-scribble" class="box-style active-buttons{if $tab eq "scribble"} active{/if}" id="home-tab-scribble">Posts</li>
            <li divId="home-tab-content-news"  class="box-style active-buttons {if $tab eq "news"} active{/if}" id="home-tab-news">News</li>
            <li divId="home-tab-content-bawraa"  class="box-style active-buttons {if $tab eq "bawraas"} active{/if}" id="home-tab-bawraas">Bawraas<sup style="color: #f16667;">NEW!</sup></li>
        </ul>
    </div>
    <div class="tab-content">

        <div class="home-right-tab-content  {if $tab neq ""} display-none{elseif $tab eq ""} active-tab-content{/if}" id="home-tab-content-events">
            {include file="../templates/wistie/events_tab_content.tpl"}
        </div>
        <div class="home-right-tab-content  {if $tab neq "scribble"} display-none{elseif $tab eq "scribble"} active-tab-content{/if}" id="home-tab-content-scribble">
            {include file="../templates/wistie/scribble_tab_content.tpl"}
        </div>
        <div class="home-right-tab-content {if $tab neq "news"} display-none{elseif $tab eq "news"} active-tab-content{/if}" id="home-tab-content-news">
            {include file="../templates/wistie/news_tab_content.tpl"}
        </div>
        <div class="home-right-tab-content {if $tab neq "bawraas"} display-none{elseif $tab eq "bawraas"} active-tab-content {/if}" id="home-tab-content-bawraa">
            {include file="../templates/wistie/bawraa_tab.tpl"}
        </div>
    </div>
    <div id="quip-browse-bar" class="alert-bar transitionable">
        <div id="alert-bar-inner">
            <span id="quip-browse-bar-copy">New Year Events</span>
            <a id="quip-browse-bar-findUrl" class="primary button inlineblock" href="http:\\shaukk.com">Shaukk</a>
        </div>
    </div>
</div>




